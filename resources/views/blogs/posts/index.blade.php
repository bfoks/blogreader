@extends('layouts.page')

@section('content')

    <div class="container mx-auto mt-8">

        <div class="py-2 px-2 text-black text-2xl">
            {{ $blog->name }}
        </div>

        <div class="mx-1 shadow rounded-sm border">

            @forelse($blog->posts as $post)

                <div id="{{ $post->id }}"
                     class="flex justify-between text-center items-center bg-white hover:bg-grey-lightest hover:text-orange-light relative py-4">

                    <div class="text-grey-darker px-4 font-mono">
                        @if($readPosts->contains($post->id))
                            <span title="read post" style="background-color: #97ce76"
                                  class="inline-flex text-4xl text-white items-center justify-center rounded-full h-10 w-10">&#x2713;</span>
                        @else
                            <span title="unread post" style="background-color: #eee"
                                  class="inline-flex text-4xl text-white items-center justify-center rounded-full h-10 w-10">&#x2713;</span>
                        @endif
                    </div>
                    <div class="w-3/5 flex-grow lg:pr-1 text-xl text-left">
                        <a href="{{ url(route('blogs.posts.show', [$blog, $post], false), [], false) }}">{{ $post->title }}</a>
                    </div>
                    <div class="hidden lg:block lg:w-1/6 text-grey-dark text-left">
                        <span title="{{ $post->datetime_utc }}">{{ \Carbon\Carbon::createFromTimeString($post->datetime_utc)->diffForHumans() }}</span>
                    </div>

                </div>

            @empty
                <div class="text-center leading-loose p-4 bg-white">
                    <p class="text-xl">This blog has no posts</p>
                </div>
            @endforelse
        </div>

        <div class="my-4 text-right">
            <a class="px-2" href="#navbar">back to top</a>
        </div>
    </div>

@endsection