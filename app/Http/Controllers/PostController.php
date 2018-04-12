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

        /** @var Collection $usersReadPosts */
        $usersReadPosts = collect([]);

        if (auth()->user()) {
            $usersReadPosts = DB::table('posts')
                ->join('users_read_posts', 'posts.id', '=', 'users_read_posts.post_id')
                ->where('posts.blog_id', '=', $blog->id)
                ->where('users_read_posts.user_id', '=', auth()->user()->id)
                ->pluck('posts.id');
        }

        return view('blogs.posts.index', ['blog' => $blog, 'usersReadPosts' => $usersReadPosts]);
    }

    public function show(Blog $blog, Post $post, Request $request)
    {

        if ($request->has('prev')) {
            /** @var Post $previousPost */
            $previousPost = $post->getPreviousPost();

            if (is_null($previousPost)) {
                return redirect(route('blogs.posts.show', [$blog, $post], false), 302, [], false)
                    ->with('flash_message', 'Brak wcześniejszych wpisów.')
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
                    ->with('flash_message', 'Brak nowszych wpisów.')
                    ->with('hide_next_button', true);

            }

        }

        //TODO: write a test
        if (auth()->user()) {
            auth()->user()->posts()->syncWithoutDetaching($post);
        }

        return view('blogs.posts.show')->with('post', $post);
    }

}
