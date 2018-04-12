<?php

namespace App\Platforms\Clients;

use App\Blog;
use App\BlogIndexingProgress;
use App\Downloaders\Guzzle;
use App\Events\BlogIndexingProgressUpdated;
use App\Platforms\Exceptions\BlogNameNotFoundException;
use App\Platforms\Exceptions\FirstPostNotFoundException;
use App\Platforms\Exceptions\NextPostNotFoundException;
use App\Post;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

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

    public function findAllPosts(Blog $blog): ?Collection
    {
        if (!$blogDomain = parse_url($blog->getUrl(), PHP_URL_HOST)) {
            throw new \Exception();
        }

        $apiUrl = "https://public-api.wordpress.com/wp/v2/sites/{$blogDomain}/posts";

        $allBlogPosts = [];

        try {

            $page = 1;
            $nextPageExists = true;
            $PER_PAGE = 50;
            $SLEEP_FOR_N_SECONDS = 1;

            while ($nextPageExists) {

                $fullApiUrl = $apiUrl . '?' . http_build_query(['per_page' => $PER_PAGE, 'page' => $page]);

                /** @var ResponseInterface $onePageOfPostsResponse */
                $onePageOfPostsResponse = $this->httpClient->download('GET', $fullApiUrl);
                $bodyAsArray = $this->jsonDecode($onePageOfPostsResponse->getBody()->__toString());

                /** @var array $allBlogPosts */
                $allBlogPosts = array_merge($allBlogPosts, $bodyAsArray);

                if (!isset($onePageOfPostsResponse->getHeader('link')[0])) {
                    throw new \Exception('NO LINK HEADER');
                }

                /** @var string $linkHeader */
                $linkHeader = $onePageOfPostsResponse->getHeader('link')[0];

                if (strpos($linkHeader, 'rel="next"') === false) {
                    $nextPageExists = false;
                } else {
                    $page++;
                    sleep($SLEEP_FOR_N_SECONDS);
                }

                // TODO:: test & others clients implementation
                BlogIndexingProgressUpdated::dispatch(new BlogIndexingProgress(count($allBlogPosts), $blog->getTotalPosts()));

            }

            return collect($allBlogPosts)->transform(function ($item, $key) use ($blog) {
                return $this->makeNewPost($blog, $item);
            });


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
            'title' => html_entity_decode($newPostRaw['title']['rendered']),

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