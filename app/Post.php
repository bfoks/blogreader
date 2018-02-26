<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    public function getDatetimeUtc()
    {
        return $this->datetime_utc;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getPreviousPost()
    {
        return $this->where('blog_id', $this->blog_id)
            ->where('datetime_utc', '<', $this->datetime_utc)
            ->orderBy('datetime_utc', 'desc')->first();
    }

    public function getNextPost()
    {
        $nextPost = self::where('blog_id', $this->blog_id)
            ->where('datetime_utc', '>', $this->datetime_utc)
            ->orderBy('datetime_utc', 'asc')->first();

        if (!is_null($nextPost)) {
            return $nextPost;
        }

        // try to find a next post
        /** @var Blog $blog */
        $blog = $this->blog->saveNextPost();

        return $blog;
    }

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

}
