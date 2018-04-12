<blog-create-form inline-template>

    <div class="container mx-auto h-full">


        <div class="flex flex-col justify-center items-center h-full w-full">


            <progress class="w-full md:w-2/3" :class="{'opacity-100': blogSubmitInProgress}" :value="percentageIndexingValue" max="105"></progress>

            <form class="flex w-full md:w-2/3 justify-center border-t-2 border-orange-lighter mx-2"
                  action="{{ route('blogs.store') }}"
                  method="POST"
                  @submit.prevent="submitBlog"
                  ref="blogForm"
            >
                {{ csrf_field() }}

                <div class="flex-1">
                    <input ref="url" class="p-4 w-full lg:text-xl border rounded-sm{{ $errors->has('url') ? ' border-red': '' }}" type="text" name="url" placeholder="Adres bloga" required>
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
                    <button  class="relative btn btn-primary p-4 lg:text-xl rounded-r-sm rounded-l-none"
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

    </div>

</blog-create-form>