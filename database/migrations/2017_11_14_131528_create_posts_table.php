<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('instagram_id')->unique();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
//            $table->bigInteger('user_id');
//            $table->foreign('user_id')->references('id')->on('users');
            $table->string('type');
            $table->string('url');
            $table->integer('comments');
            $table->integer('likes');
            $table->integer('instagram_created_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("posts", function ($table) {
            $table->dropSoftDeletes();
        });
    }
}