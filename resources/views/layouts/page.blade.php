@extends('layouts.base')


<div id="wrapper" class="flex flex-col flex-no-wrap h-full">

{{-- Navbar --}}
    <div class="flex flex-no-shrink justify-between items-center bg-white px-4 py-2 border-b-2 border-indigo">
        <div><a class="no-underline  text-sm sm:text-2xl" href="{{ route('index') }}">
                @include('_partials.logo')
            </a></div>
        <div>
            @guest
                <a class="btn btn-primary py-1" href="{{ route('register') }}">Rejestracja</a>
                <a class="btn py-1" href="{{ route('login') }}">Logowanie</a>
            @else
                <a class="btn btn-primary btn-sm" href="{{ route('blogs.index') }}">Moje blogi</a>
                <span>
                <form class="inline" action="{{ route('logout') }}" method="POST">
                @csrf
                    <button type="submit" class="btn btn-sm">Wyloguj</button>
                </form>
            </span>
            @endguest
        </div>
    </div>

    @yield('content')

</div>
