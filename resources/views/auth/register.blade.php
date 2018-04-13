@extends('layouts.page')

@section('content')
    <div class="flex items-center justify-center h-full container mx-auto">

        <form autocomplete="on" class="w-full sm:w-1/2 lg:1/3 flex items-center justify-center flex-col border-t-4 border-orange rounded shadow mx-1" method="POST" action="{{ route('register') }}">

            <div class="flex w-full items-center justify-center flex-col border-r border-b border-l border border-grey-light bg-white p-6">

                @csrf

                <input autocomplete="email" id="email" type="email" class="my-2 p-2 border w-full" name="email" value="{{ old('email') }}" placeholder="Email" required>

                @if ($errors->has('email'))
                    <span class="text-red text-sm">
                    <span>{{ $errors->first('email') }}</span>
                </span>
                @endif

                <input autocomplete="new-password" id="password" type="password" class="my-2 p-2 border w-full" name="password" placeholder="Password" required>

                @if ($errors->has('password'))
                    <span class="text-red text-sm">
                    <span>{{ $errors->first('password') }}</span>
                </span>
                @endif

                <input autocomplete="new-password" id="password-confirm" type="password" class="my-2 p-2 border w-full" name="password_confirmation" placeholder="Repeat Password"
                       required>

                <button type="submit" class="btn mt-2">
                    Create account
                </button>
            </div>

        </form>

    </div>
@endsection
