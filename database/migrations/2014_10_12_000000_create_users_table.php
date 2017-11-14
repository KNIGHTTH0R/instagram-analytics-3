<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->primary('id');
            $table->bigInteger('instagram_id')->unique();
            $table->string('username');
            $table->string('token');
            $table->integer('media');
            $table->integer('follows');
            $table->integer('followed_by');
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
        Schema::table("users", function ($table) {
            $table->dropSoftDeletes();
        });
    }
}