<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Platforms\Clients\Client;
use App\Platforms\Discoverer;
use App\Platforms\Exceptions\BlogNameNotFoundException;
use App\Platforms\Exceptions\FirstPostNotFoundException;
use App\Platforms\Exceptions\UnknownPlatformException;
use Illuminate\Http\Request;

class BlogController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'destroy', 'index', 'store']);
    }

    public function index()
    {
        /** @var Blog[] $usersBlogs */
        $usersBlogs = auth()->user()->blogs;

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
            /** @var Blog $blog */
            $blog = new Blog([
                'url' => rtrim($request->url, '/'),
                'user_id' => auth()->user()->id
            ]);

            $discoverer = new Discoverer();

            /** @var Client $client */
            $client = $discoverer->discoverClientForBlog($blog);
            $blog->setClient($client);

            $blog->initializeAndSave();

            return redirect()->route('blogs.posts.index', [$blog]);

        } catch (UnknownPlatformException $exception) {
            return redirect()->back()->with('flash_message', 'Nieobsługiwany blog ( ͡° ʖ̯ ͡°)');
        } catch (FirstPostNotFoundException $exception) {
            return redirect()->back()->with('flash_message', 'Podany blog nie posiada żadnych wpisów.');
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
        $this->authorize('delete', $blog);

        $blog->delete();

        return redirect()->route('blogs.index');
    }
}
