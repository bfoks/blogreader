<?php

namespace App\Platforms\Clients;

use App\Blog;
use App\Platforms\ClientsProvider;
use App\Post;

abstract class Client
{
    public abstract function findFirstPostFor(Blog $blog): Post;

    public abstract function findNextPostFor(Blog $blog): Post;

    /**
     * @param Blog $blog
     * @return string
     * @throws BlogNameNotFoundException
     */
    public abstract function findBlogName(Blog $blog): string;

    public abstract function findTotalPosts(Blog $blog): ?int;

    public function getClientName(): string
    {
        $clientsProvider = app()->make(ClientsProvider::class);
        return $clientsProvider->getClientKeyByClass($this);
    }
}