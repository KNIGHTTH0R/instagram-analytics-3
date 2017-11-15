<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use App\Post;
use App\User;
use App\LastPost;

class Instagram
{
    const API_URL = 'https://api.instagram.com/v1/';
    private static $client = null;

    private static function getClient()
    {
        if (!self::$client) {
            self::$client = new Client();
        }

        return self::$client;
    }

    private static function makeCall($url, $option)
    {
        try {
            $client = self::getClient();
            $response = $client->get($url, ['query' => $option, 'timeout' => 10, 'connect_timeout' => 10]);

            return json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getUserData($user_id, $access_token)
    {
        $url = self::API_URL . 'users/' . $user_id . '/';
        $options = ['access_token' => $access_token];
        $response = self::makeCall($url, $options);

        return $response->data;
    }

    public static function getUserPosts($user_id, $access_token)
    {
        $url = self::API_URL . 'users/' . $user_id . '/media/recent/';
        $options = ['access_token' => $access_token, 'count' => 10];
        $response = self::makeCall($url, $options);

        return $response->data;
    }

    public static function func($instagram_uid, $access_token)
    {
        $user = User::where('instagram_id', $instagram_uid)->first();
        $last_post = $user->getLastPost();
        $last_post_id = isset($last_post->last_post_id) ? $last_post->last_post_id : null;
        $url = self::API_URL . 'users/' . $instagram_uid . '/media/recent/';
        $options = ['access_token' => $access_token, 'count' => 10];
        $flag = true;
        $array_post_id = [];
        $posts = [];

        while ($flag) {
            $response = self::makeCall($url, $options);
            $posts = $response->data;
            $length = count($posts);

            for ($i = 0; $i < $length; $i++) {
                $post = $posts[$i];
                if (self::validatePost($post, $instagram_uid, $last_post_id)) {
                    $user->storePost($post);
                    $array_post_id[] = $post->id;
                } else {
                    $flag = false;
                    break;
                }
            }

            if ($flag && isset($response->pagination) && isset($response->pagination->next_max_id)) {
                $options['max_id'] = $response->pagination->next_max_id;
            } else {
                $flag = false;
            }
        }

        if ($array_post_id)
            self::updateLastPostId($user->id, $posts[0]->id);

        return $array_post_id;
    }

    private static function updateLastPostId($user_id, $last_post_id)
    {
        if (!($last_post = LastPost::where('user_id', $user_id)->first())) {
            $last_post = new LastPost();
            $last_post->user_id = $user_id;
        }

        $last_post->last_post_id = $last_post_id;
        $last_post->save();
    }

    private static function validatePost($post, $instagram_uid, $last_post_id)
    {
        if (!$last_post_id) {
            $user = User::where('instagram_id', $instagram_uid)->first();
            if ($post->created_time < $user->created_at->timestamp) return false;
        }

        if ($post->id == $last_post_id) return false;

//        if (!Post::find($post->id)) {
//            return true;
//        } else {
//            return false;
//        }

        return true;
    }

    public static function getRecentPostData($post_id, $access_token)
    {
        $url = self::API_URL . 'media/' . $post_id . '/';
        $options = ['access_token' => $access_token];
        $response = self::makeCall($url, $options);

        return $response->data;
    }
}