<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LastPost extends Model
{
    protected $table = 'last_post';

    public $timestamps = false;
}
