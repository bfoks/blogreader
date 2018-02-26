@extends('layouts.page')

@section('content')
    <blog-create-form inline-template>
        <div class="flex justify-center items-center h-full">
            <form class="flex justify-center border-t-2 border-indigo-lighter"
                  action="{{ route('blogs.store') }}"
                  method="POST"
                  @submit.prevent="submitBlog"
                  ref="blogForm"
            >
                {{ csrf_field() }}

                {{--<div>--}}
                {{--<input class="p-4 text-xl border rounded-sm{{ $errors->has('name') ? ' border-red': '' }}" type="text" name="name" placeholder="Nazwa" required>--}}
                {{--<p class="py-2">--}}
                {{--&nbsp;--}}
                {{--@if ($errors->has('name'))--}}
                {{--<span class="text-red text-sm">--}}
                {{--{{ $errors->first('name') }}--}}
                {{--</span>--}}
                {{--@endif--}}
                {{--</p>--}}
                {{--</div>--}}

                <div>
                    <input class="p-4 text-xl border rounded-sm{{ $errors->has('url') ? ' border-red': '' }}" type="text" name="url" placeholder="Adres bloga" required>
                    <p class="py-2">
                        &nbsp;
                        @if ($errors->has('url'))
                            <span class="text-red text-sm">
                            {{ $errors->first('url') }}
                        </span>
                        @endif

                        @if (session('flash_message'))
                            <span class="text-red text-sm">
                        {{ session('flash_message') }}
                    </span>
                        @endif

                    </p>
                </div>

                <div>
                    <button  class="relative btn btn-primary p-4 text-xl rounded-r-sm rounded-l-none"
                             :class="{'loader': blogSubmitInProgress}"
                             :disabled="blogSubmitInProgress"
                             type="submit"
                    >
                        Dodaj
                    </button>
                    <p class="py-2">&nbsp;</p>
                </div>

            </form>
        </div>
    </blog-create-form>
@endsection