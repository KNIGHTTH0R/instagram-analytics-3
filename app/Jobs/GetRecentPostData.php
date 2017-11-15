<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\Instagram;
use App\PostsMeta;
use App\Post;

class GetRecentPostData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $instagram_post_id;
    private $token;
    private $position_time;
    private $post = null;

    public $tries = 10;
    public $timeout = 60;
    public $retry_after = 70;


    /**
     * GetRecentPostData constructor.
     * @param $post_id
     * @param $token
     * @param int $position_time
     */
    public function __construct($instagram_post_id, $token, $position_time = 1)
    {
        $this->instagram_post_id = $instagram_post_id;
        $this->token = $token;
        $this->position_time =  $position_time;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->position_time > 30) return;

        $this->post = Post::where('instagram_id', $this->instagram_post_id)->first();
        $recent_post_data = Instagram::getRecentPostData($this->instagram_post_id, $this->token);

        $recent_likes = $recent_post_data->likes->count;
        $recent_comments = $recent_post_data->comments->count;
        $diff_likes = $recent_likes - $this->post->likes;
        $diff_comments = $recent_comments - $this->post->comments;

        $this->savePostMeta($diff_likes, $diff_comments);
        $this->updatePost($recent_likes, $recent_comments);

        if ($this->position_time > 23) {
            dispatch((new GetRecentPostData($this->instagram_post_id, $this->token, ++$this->position_time))->delay(120));
        } else {
            dispatch((new GetRecentPostData($this->instagram_post_id, $this->token, ++$this->position_time))->delay(60));
        }


//        if ($this->position_time > 23) {
//            dispatch((new GetRecentPostData($this->post_id, $this->token, ++$this->position_time))->delay(86400));
//        } else {
//            dispatch((new GetRecentPostData($this->post_id, $this->token, ++$this->position_time))->delay(3600));
//        }
    }

    private function savePostMeta($likes, $comments)
    {
        $post_meta = new PostsMeta();
        $post_meta->post_id = $this->post->id;
        $post_meta->likes = $likes;
        $post_meta->comments = $comments;
        $post_meta->save();
    }

    private function updatePost($likes, $comments)
    {
        $changes = false;
        if ($likes != $this->post->likes) {
            $changes = true;
            $this->post->likes = $likes;
        }

        if ($comments != $this->post->comments){
            $changes = true;
            $this->post->comments = $comments;
        }

        if ($changes) $this->post->save();
    }
}
