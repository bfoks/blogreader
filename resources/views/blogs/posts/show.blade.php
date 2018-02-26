@extends('layouts.base')

@section('body')
    <body class="h-full">

    <div class="flex justify-between items-center bg-grey-lightest px-4 py-2 border-b">

        <div class="md:w-1/5">
            <a class="no-underline visited:text-white hover:text-black" href="{{ route('index') }}">
                @include("_partials.logo")
            </a>
        </div>

        <p class="md:w-3/5 font-sans tracking-wide text-center">
            <a href="{{ route('blogs.index') }}">Moje blogi</a>
            &raquo;
            <span class="">{{ $post->blog->name }}</span>
        </p>

        <div class="md:w-1/5 text-right">

            @if (!session('hide_back_button'))
                <a class="btn btn-sm border-0"
                   href="{{ route('blogs.posts.show', [$post->blog, $post, 'prev']) }}">Wstecz</a>
            @endif

            @if (session('flash_message'))
                <span class="text-red text-sm">{{ session('flash_message') }}</span>
            @endif

            @if (!session('hide_next_button'))
                <a class="btn btn-primary btn-sm"
                   href="{{ route('blogs.posts.show', [$post->blog, $post, 'next']) }}">Dalej</a>
            @endif

        </div>

    </div>

    <iframe style="height: calc(100vh - 40px); width: 100%" frameborder="0" src="{{ $post->getLink() }}">
        Twoja przeglądarka nie obsługuje ramek pływających.
    </iframe>

    </body>
@endsection