<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Post;
use App\LastPost;

class User extends Model
{
    public function storePost($post)
    {
        $model = new Post();
        $model->instagram_id = $post->id;
        $model->user_id = $this->id; /////////////
        $model->type = $post->type;

        if ($post->type == 'video') {
            $model->url = $post->videos->standard_resolution->url;
        } else {
            $model->url = $post->images->standard_resolution->url;
        }

        $model->comments = $post->comments->count;
        $model->likes = $post->likes->count;
        $model->instagram_created_time =  $post->created_time;
        $model->save();
    }

    public function getLastPost()
    {
        return LastPost::where('user_id', $this->id)->first();
    }
}
