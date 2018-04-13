<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Exceptions\FollowedBlogsLimitExceededException;
use App\Platforms\Clients\Client;
use App\Platforms\Discoverer;
use App\Platforms\Exceptions\BlogHasNoPostsException;
use App\Platforms\Exceptions\BlogNameNotFoundException;
use App\Platforms\Exceptions\FirstPostNotFoundException;
use App\Platforms\Exceptions\UnknownPlatformException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(['destroy', 'index']);
    }

    public function index()
    {
        /** @var Blog[] $usersBlogs */
        $usersBlogs = auth()->user()->blogs;

        $usersBlogs = DB::table('blogs')
            ->select(['blogs.id', 'blogs.name', 'blogs.total_posts', 'blogs.url', DB::raw('COUNT(users_read_posts.post_id) as read_posts')])
            ->join('users_blogs', 'blogs.id', '=', 'users_blogs.blog_id')
            ->leftJoin('posts', 'blogs.id', '=', 'posts.blog_id')
            ->leftJoin('users_read_posts', 'posts.id', '=', 'users_read_posts.post_id')
            ->where('users_blogs.user_id', '=', auth()->user()->id)
            ->groupBy('blogs.id')
            ->get();

        return view('blogs.index')->with('blogs', $usersBlogs);
    }


    public function create()
    {
        return view('blogs.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|string|url',
        ]);

        try {

            if (auth()->user() && auth()->user()->fresh()->blogs->count() >= config('limits.max_followed_blogs')) {
                throw new FollowedBlogsLimitExceededException;
            }

            $urlComponents = parse_url($request->url);

            if (!$urlComponents) {
                throw new UnknownPlatformException; // maybe use more suitable exception
            }

            $cleanedUrl = rtrim($urlComponents['scheme'] . '://' . $urlComponents['host'] . ($urlComponents['path'] ?? ''), '/');

            if ($alreadyExistingBlog = Blog::where('url', $cleanedUrl)->first()) {

                if (auth()->user()) {
                    auth()->user()->blogs()->syncWithoutDetaching($alreadyExistingBlog);
                }

                return redirect()->route('blogs.posts.index', [$alreadyExistingBlog]);
            }

            /** @var Blog $blog */
            $blog = new Blog([
                'url' => $cleanedUrl,
            ]);

            $discoverer = new Discoverer();

            /** @var Client $client */
            $client = $discoverer->discoverClientForBlog($blog);
            $blog->setClient($client);

            $blog->initializeAndSave();

            if (auth()->user()) {
                auth()->user()->blogs()->attach($blog);
            }

            return redirect()->route('blogs.posts.index', [$blog]);

        } catch (FollowedBlogsLimitExceededException $exception) {
            return redirect()->back()->with('flash_message', 'In free beta version user can follow only up to 5 blogs');
        } catch (UnknownPlatformException $exception) {
            return redirect()->back()->with('flash_message', 'Unsupported blog ( ͡° ʖ̯ ͡°)');
        } catch (FirstPostNotFoundException | BlogHasNoPostsException $exception) {
            return redirect()->back()->with('flash_message', 'This blog has no posts');
        }
    }


    public function show(Blog $blog)
    {
        //
    }


    public function edit(Blog $blog)
    {
        //
    }


    public function update(Request $request, Blog $blog)
    {
        //
    }


    public function destroy(Blog $blog)
    {
        auth()->user()->blogs()->detach($blog);

        return redirect()->route('blogs.index');
    }
}
