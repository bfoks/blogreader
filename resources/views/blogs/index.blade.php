@extends('layouts.page')

@section('content')

    <div class="container mx-auto mt-8">

        <div class="mx-1 shadow rounded-sm border">

            <div class="flex justify-between text-center items-center text-orange-dark bg-grey-lighter">
                <div class="w-2/5 lg:w-1/5 text-xl py-2">Przeczytanych</div>
                <div class="w-3/5 lg:w-2/5 lg:text-left text-xl py-2">Blog</div>
                <div class="hidden lg:block lg:w-2/5 lg:text-left text-xl py-2">Adres</div>
            </div>

            @forelse($blogs as $blog)

                <div class="blog-entity flex justify-between text-center items-center bg-white hover:bg-grey-lightest hover:text-orange-light relative py-1">
                    <div class="w-2/5 lg:w-1/5 text-grey-darker font-mono">
                        <div class="w-24 text-left mx-auto"><span class="text-4xl">{{ $blog->read_posts }}</span><span>/{{ $blog->total_posts ?? '?' }}</span></div>
                    </div>
                    <div class="w-3/5 lg:w-2/5 text-xl text-left">
                        <a href="{{ route('blogs.posts.index', [$blog->id]) }}">{{ $blog->name }}</a>
                    </div>
                    <div class="hidden lg:block lg:w-2/5 text-grey-dark text-left font-mono truncate">
                        <span>{{ $blog->url }}</span>
                        <span class="delete hidden absolute pin-r pr-6">
                        <form action="{{ route('blogs.destroy', [$blog->id]) }}" method="POST">
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

    </div>

    @if(count($blogs))
        <div class="container mx-auto mt-4 text-right">
            <div class="mx-1 mb-4">
                <a href="{{ route('blogs.create') }}" class="btn btn-primary">
                    Dodaj nowy
                </a>
            </div>
        </div>
    @endisset

@endsection