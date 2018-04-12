@extends('layouts.base')

@section('body')
    <body class="h-full">

    <div class="flex justify-between items-center bg-grey-lightest px-4 py-2 border-b">

        <div class="sm:w-1/5">
            <a class="" href="{{ route('index') }}">@include("_partials.logo")</a>
            {{--<a class="sm:hidden" href="{{ route('index') }}">br.pro</a>--}}
        </div>

        <p class="hidden sm:block sm:w-3/5 font-sans tracking-wide text-center">
            <a href="{{ route('blogs.index') }}">Moje blogi</a>
            &raquo;
            <a href="{{ route('blogs.posts.index', [$post->blog]) }}#{{ $post->id }}">{{ $post->blog->name }}</a>
        </p>

        <div class="sm:w-1/5 text-right">

            @if (!session('hide_back_button'))
                <a class="btn btn-sm border-0"
                   href="{{ url(route('blogs.posts.show', [$post->blog, $post, 'prev'], false), [], false) }}">Wstecz</a>
            @endif

            @if (session('flash_message'))
                <span class="text-red text-sm">{{ session('flash_message') }}</span>
            @endif

            @if (!session('hide_next_button'))
                <a class="btn btn-primary btn-sm"
                   href="{{ url(route('blogs.posts.show', [$post->blog, $post, 'next'], false), [], false) }}">Dalej</a>
            @endif

        </div>

    </div>

    <iframe style="height: calc(100vh - 45px); width: 100%" frameborder="0" src="{{ $post->getLink() }}">
        Twoja przeglądarka nie obsługuje ramek pływających.
    </iframe>

    </body>
@endsection