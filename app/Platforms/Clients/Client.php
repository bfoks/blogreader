<?php

namespace App\Platforms\Clients;

use App\Blog;
use App\Platforms\ClientsProvider;
use App\Platforms\Exceptions\BlogHasNoPostsException;
use App\Platforms\Exceptions\BlogNameNotFoundException;
use App\Post;
use Illuminate\Support\Collection;

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

    /**
     * @param Blog $blog
     * @return int|null
     * @throws BlogHasNoPostsException
     */
    public abstract function findTotalPosts(Blog $blog): ?int;

    public abstract function findAllPosts(Blog $blog): ?Collection;

    public function getClientName(): string
    {
        $clientsProvider = app()->make(ClientsProvider::class);
        return $clientsProvider->getClientKeyByClass($this);
    }
}