<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Platforms\Clients\Client;
use App\Platforms\Exceptions\NextPostNotFoundException;
use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['index', 'show']);
    }

    public function index(Blog $blog, Request $request)
    {
        $this->authorize('view', $blog);

        $latestBlogPost = $blog->posts()->latest('datetime_utc')->firstOrFail();

        return redirect()->route('blogs.posts.show', [$blog, $latestBlogPost]);
    }

    public function show(Blog $blog, Post $post, Request $request)
    {
        $this->authorize('view', $post);

        if ($request->has('prev')) {
            /** @var Post $previousPost */
            $previousPost = $post->getPreviousPost();

            if (is_null($previousPost)) {
                return redirect()->route('blogs.posts.show', [$blog, $post])
                    ->with('flash_message', 'Brak wcześniejszych wpisów.')
                    ->with('hide_back_button', true);
            }

            return redirect()->route('blogs.posts.show', [$blog, $previousPost]);
        }

        if ($request->has('next')) {

            try {
                /** @var Post $nextPost */
                $nextPost = $post->getNextPost();

                return redirect()->route('blogs.posts.show', [$blog, $nextPost]);

            } catch (NextPostNotFoundException $exception) {

                return redirect()->route('blogs.posts.show', [$blog, $post])
                    ->with('flash_message', 'Brak nowszych wpisów.')
                    ->with('hide_next_button', true);

            }

        }

        return view('blogs.posts.show')->with('post', $post);
    }

}
