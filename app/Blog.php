<?php

namespace App;

use App\Platforms\Clients\FakeWordpress;
use App\Platforms\Clients\Wordpress;
use App\Platforms\Clients\Client;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $guarded = [];

    /** @var Wordpress|FakeWordpress|Client client */
    protected $client;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->client = \App::make(Platforms\Clients\Client::class);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function initializeAndSave()
    {
        $this->name = $this->client->findBlogName($this);
        $firstPost = $this->client->findFirstPostFor($this);

        $this->save();
        $this->posts()->save($firstPost);

        return $this;
    }

    public function saveNextPost()
    {
        $post = $this->client->findNextPostFor($this);
        $post->save();

        return $post;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

}
