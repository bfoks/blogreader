@extends('layouts.base')

@section('body')

    <div id="wrapper" class="flex flex-col flex-no-wrap h-full">

        {{-- Navbar --}}
        <div class="z-20 flex flex-no-shrink justify-between items-center px-4 py-2 border-b-2 border-orange">
            <div><a class="no-underline  text-sm sm:text-2xl" href="{{ route('index') }}">
                    @include('_partials.logo')
                </a></div>
            <div>
                @guest
                    <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Rejestracja</a>
                    <a class="btn btn-sm" href="{{ route('login') }}">Logowanie</a>
                @else
                    <a class="btn btn-primary btn-sm border-0" href="{{ route('blogs.index') }}">Moje blogi</a>
                    <span>
                        <form class="inline m-0 p-0" action="{{ route('logout') }}" method="POST">
                        @csrf
                            <button type="submit" class="btn btn-sm">Wyloguj</button>
                        </form>
                    </span>
                @endguest
            </div>
        </div>

        @yield('content')

    </div>

@endsection
