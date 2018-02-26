@extends('layouts.page')

@section('content')

    <div class="container mx-auto border mt-8 rounded-sm shadow">

        <div class="flex justify-between text-center items-center text-indigo-dark bg-grey-light">
            <div class="w-1/5 text-xl py-2">Przeczytanych</div>
            <div class="text-left w-2/5 text-xl py-2">Blog</div>
            <div class="text-left w-2/5 text-xl py-2">Adres</div>
        </div>
        @forelse($blogs as $blog)

            <div
               class="blog-entity flex justify-between text-center items-center bg-white hover:bg-grey-lightest hover:text-indigo-light relative">
                <div class="w-1/5 text-grey-darkest text-5xl">{{ $blog->posts()->count() }}</div>
                <div class="w-2/5 text-grey-darkest text-xl text-left">
                    <a href="{{ route('blogs.posts.index', [$blog]) }}">{{ $blog->name }}</a>
                </div>
                <div class="w-2/5 text-grey-darkest text-xl text-left font-mono truncate">
                    <span>{{ $blog->url }}</span>
                    <span class="delete hidden absolute pin-r pr-6">
                        <form action="{{ route('blogs.destroy', [$blog]) }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button class="text-grey-darker" title="Usuń z listy" type="submit">X</button>
                        </form>
                    </span>
                </div>
            </div>

        @empty
            <div class="text-center leading-loose p-4 bg-white">
                <p class="text-xl">Wygląda na to, że nie obserwujesz jeszcze żadnego bloga.</p>
                <p class="mt-4"><a class="btn" href="{{ route('blogs.create') }}">Dodaj pierwszy (✌ ﾟ ∀ ﾟ)☞</a></p>
            </div>
        @endforelse

    </div>

    @if(count($blogs))
        <div class="container mx-auto mt-4 text-right">
            <a href="{{ route('blogs.create') }}" class="btn-primary">
                Dodaj nowy
            </a>
        </div>
    @endisset

@endsection