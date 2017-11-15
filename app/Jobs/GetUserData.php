<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helpers\Instagram;
use App\Jobs\GetUserPosts;
use App\User;

class GetUserData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;
    private $token;
    public $tries = 10;
    public $timeout = 60;
    public $retry_after = 70;

    /**
     * GetUserData constructor.
     * @param $id
     * @param $token
     */
    public function __construct($id, $token)
    {
        $this->id = $id;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = Instagram::getUserData($this->id, $this->token);
        $this->saveUser($data);

        dispatch((new GetUserPosts($this->id, $this->token))->delay(30));
//        dispatch((new GetUserPosts($this->id, $this->token))->delay(3600));
    }

    private function saveUser($data)
    {
        $user = new User();
        $user->instagram_id = $this->id;
        $user->username = $data->username;
        $user->media = $data->counts->media;
        $user->follows = $data->counts->follows;
        $user->followed_by = $data->counts->followed_by;
        $user->token = $this->token;
        $user->save();
    }
}
