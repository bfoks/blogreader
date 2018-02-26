<?php

namespace App\Platforms\Clients;

use App\Blog;
use App\Post;

interface Client
{
    public function findFirstPostFor(Blog $blog): Post;

    public function findNextPostFor(Blog $blog): Post;

    public function findBlogName(Blog $blog): string;
}