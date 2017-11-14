<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class Instagram
{
    const API_URL = 'https://api.instagram.com/v1/';

    private static $client = null;

    public function __construct() {}

    private function getClient()
    {
        if (!self::$client) {
            self::$client = new Client();
        }

        return self::$client;
    }

    private function makeCall($url, $option)
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
        $url = self::API_URL . 'users/' . $user_id;
        $options = ['access_token' => $access_token];

        return self::makeCall($url, $options);
    }

    public static function getPosts($user_id, $access_token)
    {
        $url = self::API_URL . 'users/' . $user_id . '/media/recent';
        $options = ['access_token' => $access_token, 'count' => 10];

        return self::makeCall($url, $options);
    }
}