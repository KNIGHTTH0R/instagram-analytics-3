<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Instagram;
use App\User;

class UserController extends Controller
{
    public function getUserData(Request $request)
    {
        $user = User::where('instagram_id', '4236259779')->first();

        return json_encode($user->getLastPost());
//        $data = Instagram::getUserData(4236259779, '4236259779.f807915.35cebf51d3d441cbb23e88be8b1518fa');
//        $user = new User();
//        $user->id = $data->id;
//        $user->instagram_id = $data->id;
//        $user->username = $data->username;
//        $user->media = $data->counts->media;
//        $user->follows = $data->counts->follows;
//        $user->followed_by = $data->counts->followed_by;
//        $user->token = '4236259779.f807915.35cebf51d3d441cbb23e88be8b1518fa';
//        $user->save();
//        return json_encode($user);
    }

    public function getUserPosts(Request $request)
    {
        $data = Instagram::getUserPosts(4236259779, '4236259779.f807915.35cebf51d3d441cbb23e88be8b1518fa');
        return json_encode($data);
    }
}
