<?php

namespace App\Platforms\Clients;

use App\Blog;
use App\Downloaders\Guzzle;
use App\Platforms\Exceptions\BlogNameNotFoundException;
use App\Platforms\Exceptions\FirstPostNotFoundException;
use App\Platforms\Exceptions\NextPostNotFoundException;
use App\Post;

class GlobalWP extends Client
{
    /** @var Guzzle */
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = \App::make(Guzzle::class);
    }

    /**
     * @param Blog $blog
     * @return Post
     * @throws \Exception
     */
    public function findFirstPostFor(Blog $blog): Post
    {
        if (!$blogDomain = parse_url($blog->getUrl(), PHP_URL_HOST)) {
            throw new \Exception();
        }

        $fullApiUrl = "https://public-api.wordpress.com/wp/v2/sites/{$blogDomain}/posts/?orderby=date&order=asc&per_page=1";

        try {

            $body = $this->httpClient->downloadBody('GET', $fullApiUrl);
            $bodyAsArray = $this->jsonDecode($body);
            $firstPostRaw = $this->extractFirstPost($bodyAsArray);

            return $this->makeNewPost($blog, $firstPostRaw);

        } catch (\Exception $exception) {
            throw new FirstPostNotFoundException();
        }

    }

    /**
     * @param Blog $blog
     * @return Post
     * @throws \Exception
     */
    public function findNextPostFor(Blog $blog): Post
    {
        if (!$blogDomain = parse_url($blog->getUrl(), PHP_URL_HOST)) {
            throw new \Exception();
        }

        /** @var Post $recentLocalPost */
        $recentLocalPost = $blog->posts()->latest('datetime_utc')->first();
        $startDate = $this->iso8601($recentLocalPost->getDatetime());

        $fullApiUrl = "https://public-api.wordpress.com/wp/v2/sites/{$blogDomain}/posts/?orderby=date&order=asc&per_page=1&after={$startDate}";

        try {
            $body = $this->httpClient->downloadBody('GET', $fullApiUrl);
            $bodyAsArray = $this->jsonDecode($body);
            $nextPostRaw = $this->extractFirstPost($bodyAsArray);

            return $this->makeNewPost($blog, $nextPostRaw);

        } catch (\Exception $exception) {
            throw new NextPostNotFoundException();
        }

    }

    /**
     * @param Blog $blog
     * @return string
     * @throws BlogNameNotFoundException
     */
    public function findBlogName(Blog $blog): string
    {
        if (!$blogDomain = parse_url($blog->getUrl(), PHP_URL_HOST)) {
            throw new \Exception();
        }

        $fullApiUrl = "https://public-api.wordpress.com/rest/v1.2/sites/{$blogDomain}/";

        try {

            $body = $this->httpClient->downloadBody('GET', $fullApiUrl);
            $bodyAsArray = $this->jsonDecode($body);

            return $this->extractBlogName($bodyAsArray);

        } catch (\Exception $exception) {
            throw new BlogNameNotFoundException();
        }

    }

    public function findTotalPosts(Blog $blog): ?int
    {
        if (!$blogDomain = parse_url($blog->getUrl(), PHP_URL_HOST)) {
            throw new \Exception();
        }

        $fullApiUrl = "https://public-api.wordpress.com/wp/v2/sites/{$blogDomain}/posts";

        try {

            $totalPostsHeaderValues = $this->httpClient->downloadHeader('HEAD', $fullApiUrl, 'x-wp-total');

            if (isset($totalPostsHeaderValues[0]) && $totalPostsHeaderValues[0] !== '') {
                return $totalPostsHeaderValues[0];
            }

            return null;

        } catch (\Exception $exception) {
            throw $exception;
        }

    }

    /**
     * @param string $datetime
     * @return string
     */
    public function iso8601(string $datetime): string
    {
        return (new \DateTime($datetime))->format('Y-m-d\TH:i:s');
    }

    /**
     * @param array $rawPosts
     * @return array
     * @throws \Exception
     */
    protected function extractFirstPost(array $rawPosts): array
    {
        if (empty($rawPosts[0])) {
            throw new \Exception('EMPTY FIRST POST');
        }

        return $rawPosts[0];
    }

    /**
     * @param string $json
     * @return array
     * @throws \Exception
     */
    protected function jsonDecode(string $json): array
    {
        $array = json_decode($json, true);

        if (is_null($array)) {
            throw new \Exception('JSON NULL');
        }

        return $array;
    }

    /**
     * @param Blog $blog
     * @param array $newPostRaw
     * @return Post
     */
    protected function makeNewPost(Blog $blog, array $newPostRaw): Post
    {
        return new Post([
            'local_id' => $newPostRaw['id'],
            'datetime' => $newPostRaw['date'],
            'datetime_utc' => $newPostRaw['date_gmt'],
            'link' => $newPostRaw['link'],
            'title' => $newPostRaw['title']['rendered'],

            'blog_id' => $blog->id
        ]);
    }

    /**
     * @param array $bodyAsArray
     * @return string
     * @throws \Exception
     */
    protected function extractBlogName(array $bodyAsArray): string
    {
        if (empty($bodyAsArray['name'])) {
            throw new \Exception('NO NAME BLOG');
        }

        return $bodyAsArray['name'];
    }


}