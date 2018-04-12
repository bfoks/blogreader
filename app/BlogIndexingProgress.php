<?php

namespace App;


class BlogIndexingProgress
{

    /** @var int $indexedPosts */
    private $numberOfIndexedPosts;
    /** @var int $numberOfTotalPosts */
    private $numberOfTotalPosts;

    public function __construct(int $numberOfIndexedPosts, int $numberOfTotalPosts)
    {
        $this->numberOfIndexedPosts = $numberOfIndexedPosts;
        $this->numberOfTotalPosts = $numberOfTotalPosts;
    }

    public function getPercentageProgress()
    {
        return (int) ($this->numberOfIndexedPosts / $this->numberOfTotalPosts * 100);
    }

}