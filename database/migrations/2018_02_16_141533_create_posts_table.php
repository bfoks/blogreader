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
            $table->string('title');
            $table->integer('local_id');
            $table->string('link');
            $table->dateTime('datetime');
            $table->dateTime('datetime_utc');
            $table->unsignedInteger('blog_id');
            $table->timestamps();

            // TODO: write a test
            $table->unique(['blog_id', 'local_id']);

            $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['blog_id']);
        });

        Schema::dropIfExists('posts');
    }
}
