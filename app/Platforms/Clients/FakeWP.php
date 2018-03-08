<?php

namespace App\Platforms\Clients;


use App\Blog;
use App\Platforms\Exceptions\BlogNameNotFoundException;
use App\Platforms\Exceptions\FirstPostNotFoundException;
use App\Platforms\Exceptions\NextPostNotFoundException;
use App\Post;

class FakeWP extends Client
{
    protected $config = [
        'findBlogName' => true,
        'findFirstPost' => true,
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    public function findFirstPostFor(Blog $blog): Post
    {
        if (!$this->config['findFirstPost']) {
            throw new FirstPostNotFoundException;
        }

        return new Post([
            'title' => "First post's title",
            'local_id' => 70,
            'link' => 'https://example.com/first-post/',
            'datetime' => '2017-12-31T22:00:00',
            'datetime_utc' => '2018-01-01T00:00:00',
            'blog_id' => $blog->id,
        ]);
    }

    public function findNextPostFor(Blog $blog): Post
    {
        /** @var Post $lastBlogsPost */
        $lastBlogsPost = $blog->posts()->orderBy('datetime_utc', 'desc')->firstOrFail();

        switch ($lastBlogsPost->getDatetimeUtc()) {

            case '2018-01-01 00:00:00':
                return new Post([
                    'title' => "Second post's title",
                    'local_id' => 80,
                    'link' => 'https://example.com/second-post/',
                    'datetime' => '2018-01-01T22:00:00',
                    'datetime_utc' => '2018-01-02T00:00:00',
                    'blog_id' => $blog->id,
                ]);
            default:
                throw new NextPostNotFoundException;
        }
    }


    public function findBlogName(Blog $blog): string
    {
        if ($this->config['findBlogName']) {
            return 'Example Blog Name';
        }

        throw new BlogNameNotFoundException;
    }

    public function findTotalPosts(Blog $blog): ?int
    {
        return null;
    }
}