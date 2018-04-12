<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_blogs', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('blog_id');
            $table->timestamps();

            $table->unique(['user_id', 'blog_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('blog_id')->references('id')->on('blogs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_blogs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['blog_id']);
        });

        Schema::dropIfExists('users_blogs');
    }
}
