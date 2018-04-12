@extends('layouts.page')

@section('content')

    <div style="min-height: calc(100vh - 45px)" class="relative">

        <div id="particles-js"></div>

        <div class="flex flex-wrap absolute pin-t w-full items-center justify-center min-h-full">

            <div class="px-1">

                <h1 class="text-xl sm:text-3xl m-0 text-center">Twój osobisty manager blogów</h1>

                <ul style="list-style-type: none" class="mt-8 text-xl">
                    <li class="mb-6">Wygodna nawigacja między wpisami</li>
                    <li class="mb-6">Chronologiczna kolejność wpisów</li>
                    <li class="mb-6">Powiadomienia o nowych wpisach na blogu <sup><span class="soon">wkrótce!</span></sup></li>
                    <li class="mb-6">Zapisywanie notatek dla dowolnego wpisu <sup><span class="soon">wkrótce!</span></sup></li>
                    <li class="mb-6">Grupowanie wpisów w zbiory <sup><span class="soon">wkrótce!</span></sup></li>
                    <li class="mb-6">Obsługa platformy Blogspot <sup><span class="soon">wkrótce!</span></sup></li>
                </ul>

            </div>

            <div class="px-1 w-full lg:w-1/2">
                @include('_inline-vue-templates.blog-create-form')
            </div>

        </div>

    </div>

    @push('scripts')

        <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>

        <script>
            particlesJS("particles-js", {
                "particles": {
                    "number": {"value": 60, "density": {"enable": true, "value_area": 800}},
                    "color": {"value": "#ffffff"},
                    "shape": {
                        "type": "circle",
                        "stroke": {"width": 0, "color": "#000000"},
                        "polygon": {"nb_sides": 5},
                        "image": {"src": "img/github.svg", "width": 100, "height": 100}
                    },
                    "opacity": {"value": 0.5, "random": false, "anim": {"enable": false, "speed": 1, "opacity_min": 0.1, "sync": false}},
                    "size": {"value": 3, "random": true, "anim": {"enable": false, "speed": 40, "size_min": 0.1, "sync": false}},
                    "line_linked": {"enable": true, "distance": 150, "color": "#ffffff", "opacity": 1, "width": 3},
                    "move": {
                        "enable": true,
                        "speed": 4,
                        "direction": "none",
                        "random": false,
                        "straight": false,
                        "out_mode": "out",
                        "bounce": false,
                        "attract": {"enable": false, "rotateX": 600, "rotateY": 1200}
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {"onhover": {"enable": true, "mode": "repulse"}, "onclick": {"enable": true, "mode": "push"}, "resize": true},
                    "modes": {
                        "grab": {"distance": 400, "line_linked": {"opacity": 1}},
                        "bubble": {"distance": 400, "size": 40, "duration": 2, "opacity": 8, "speed": 3},
                        "repulse": {"distance": 200, "duration": 0.4},
                        "push": {"particles_nb": 4},
                        "remove": {"particles_nb": 2}
                    }
                },
                "retina_detect": true
            });
        </script>
    @endpush

@endsection