<?php

namespace App;

use App\Platforms\Clients\FakeWP;
use App\Platforms\Clients\SelfHostedWP;
use App\Platforms\Clients\Client;
use App\Platforms\ClientsProvider;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $guarded = [];

    /** @var SelfHostedWP|FakeWP|Client client */
    protected $client;

    public function getUrl()
    {
        return $this->url;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function initializeAndSave()
    {
        $this->name = $this->client->findBlogName($this);
        $this->total_posts = $this->client->findTotalPosts($this);
        $this->platform_name = $this->client->getClientName();

        $allPosts = $this->client->findAllPosts($this);

        $this->save();
        $this->posts()->saveMany($allPosts);

        return $this;
    }

    public function findTotalPosts()
    {
        if (is_null($this->client)) {
            throw new \Exception('Client must be set before using findTotalPosts method');
        }

        return $this->client->findTotalPosts($this);
    }

    public function saveNextPost()
    {
        $clientsProvider = app()->make(ClientsProvider::class);

        $this->setClient($clientsProvider->getClientInstanceByKey($this->platform_name));

        $post = $this->client->findNextPostFor($this);
        $post->save();

        return $post;
    }

    public function getTotalPosts()
    {
        return $this->total_posts;
    }

    public function posts()
    {
        return $this->hasMany(Post::class); // ->orderBy('datetime_utc');
    }

}
