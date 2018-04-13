<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Platforms\Clients\Client;
use App\Platforms\Exceptions\NextPostNotFoundException;
use App\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{

    public function index(Blog $blog, Request $request)
    {
        $blog = $blog->load('posts');

        /** @var Collection $readPosts */
        $readPosts = collect([]);

        if (auth()->user()) {
            $readPosts = DB::table('posts')
                ->join('users_read_posts', 'posts.id', '=', 'users_read_posts.post_id')
                ->where('posts.blog_id', '=', $blog->id)
                ->where('users_read_posts.user_id', '=', auth()->user()->id)
                ->pluck('posts.id');
        } else {
            $readPosts = collect(session()->get('guest_read_posts'));
        }

        return view('blogs.posts.index', ['blog' => $blog, 'readPosts' => $readPosts]);
    }

    public function show(Blog $blog, Post $post, Request $request)
    {

        if ($request->has('prev')) {
            /** @var Post $previousPost */
            $previousPost = $post->getPreviousPost();

            if (is_null($previousPost)) {
                return redirect(route('blogs.posts.show', [$blog, $post], false), 302, [], false)
                    ->with('flash_message', 'There are no previous posts')
                    ->with('hide_back_button', true);
            }

            return redirect(route('blogs.posts.show', [$blog, $previousPost], false), 302, [], false);
        }

        if ($request->has('next')) {

            try {
                /** @var Post $nextPost */
                $nextPost = $post->getNextPost();

                return redirect(route('blogs.posts.show', [$blog, $nextPost], false), 302, [], false);

            } catch (NextPostNotFoundException $exception) {

                return redirect(route('blogs.posts.show', [$blog, $post], false), 302, [], false)
                    ->with('flash_message', 'There are no newer posts')
                    ->with('hide_next_button', true);

            }

        }

        //TODO: write a test
        if (auth()->user()) {
            auth()->user()->posts()->syncWithoutDetaching($post);
        } else {
            // TODO: this solution push the same post multiple times...
            session()->push('guest_read_posts', $post->id);
        }

        return view('blogs.posts.show')->with('post', $post);
    }

}
