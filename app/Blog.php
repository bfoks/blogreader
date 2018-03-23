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

        $firstPost = $this->client->findFirstPostFor($this);

        $this->save();
        $this->posts()->save($firstPost);

        return $this;
    }

    public function saveNextPost()
    {
        $clientsProvider = app()->make(ClientsProvider::class);

        $this->setClient($clientsProvider->getClientInstanceByKey($this->platform_name));

        $post = $this->client->findNextPostFor($this);
        $post->save();

        return $post;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

}
