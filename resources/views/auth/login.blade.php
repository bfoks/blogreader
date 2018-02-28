@extends('layouts.page')

@section('content')

    <div class="flex items-center justify-center h-full container mx-auto">

        <form class="w-full sm:w-1/2 lg:1/3 flex items-center justify-center flex-col border-t-4 border-orange rounded shadow mx-1" method="POST"
              action="{{ route('login') }}">

            <div class="flex w-full items-center justify-center flex-col border-r border-b border-l border border-grey-light bg-white p-6">

                @csrf

                <input id="email"
                       type="email"
                       class="my-2 p-2 border w-full"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="Email"
                       required autofocus
                >

                @if ($errors->has('email'))
                    <span class="text-red text-sm">
                    <span>{{ $errors->first('email') }}</span>
                </span>
                @endif

                <input id="password" type="password"
                       class="my-2 p-2 border w-full"
                       name="password"
                       placeholder="Hasło"
                       required
                >

                @if ($errors->has('password'))
                    <span class="text-red text-sm">
                    <span>{{ $errors->first('password') }}</span>
                </span>
                @endif


                <div class="my-2 text-grey-darker text-sm text-left w-full">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Zapamiętaj mnie
                    </label>
                </div>

                <button type="submit" class="btn mt-2">
                    Zaloguj
                </button>

                {{--<div class="mt-4">--}}
                    {{--<a class="text-xs" href="{{ route('password.request') }}">--}}
                        {{--Zapomniałeś/aś hasła?--}}
                    {{--</a>--}}
                {{--</div>--}}

            </div>

        </form>
    </div>
@endsection
