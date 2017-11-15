<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Post;
use App\User;
use App\Jobs\GetRecentPostData;
use App\Helpers\Instagram;

class GetUserPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $instagram_uid;
    private $token;
    public $tries = 10;
    public $timeout = 60;
    public $retry_after = 70;


    /**
     * GetUserPosts constructor.
     * @param $id
     * @param $token
     */
    public function __construct($id, $token)
    {
        $this->instagram_uid = $id;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $array_post_id = Instagram::func($this->instagram_uid, $this->token);
            if ($array_post_id) {
                foreach ($array_post_id as $post_id) {
                    dispatch((new GetRecentPostData($post_id, $this->token))->delay(30));
                }
            }

            dispatch((new GetUserPosts($this->instagram_uid, $this->token))->delay(60));
        } catch (\Exception $e) {
            echo $e;
        }

//            $posts = Instagram::getUserPosts($this->instagram_uid, $this->token);
//            $length = count($posts);
//            for ($i = 0; $i < $length; $i++) {
//                $post = $posts[$i];
//                if ($this->validatePost($post)) {
//                    $this->savePost($post);
//                    dispatch((new GetRecentPostData($post->id, $this->token))->delay(180));
////                dispatch((new GetRecentPostData($post->id, $this->token))->delay(3600 - time() % 3600 + $post->created_time % 3600));
//                }
//                /*
//                 * TODO: if there are more than 10 posts in the last hour
//                 * high queue
//                 */
//            }

//            dispatch((new GetUserPosts($this->instagram_uid, $this->token))->delay(300));
//        dispatch((new GetUserPosts($this->instagram_uid, $this->token))->delay(3600));

    }

//    private function validatePost($post)
//    {
//        $user = User::where('instagram_id', $this->instagram_uid)->first();
//        if ($post->created_time < $user->created_at->timestamp)
//            return false;
//
//        if (!Post::find($post->id)) {
//            return true;
//        } else {
//            return false;
//        }
//    }
//    private function savePost($post)
//    {
//        $model = new Post();
//        $model->id = $post->id;
//        $model->user_id = $this->instagram_uid;
//        $model->type = $post->type;
//
//        if ($post->type == 'video') {
//            $model->url = $post->videos->standard_resolution->url;
//        } else {
//            $model->url = $post->images->standard_resolution->url;
//        }
//
//        $model->comments = $post->comments->count;
//        $model->likes = $post->likes->count;
//        $model->instagram_created_time =  $post->created_time;
//        $model->save();
//    }
}
