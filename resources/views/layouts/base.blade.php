<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>blogreader.pro beta</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ mix('css/styles.css') }}" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            min-height: 100%;
            font-family: 'Nunito', sans-serif;
        }
    </style>

    @stack('styles')

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-118274127-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', 'UA-118274127-1');
    </script>

</head>

<body class="bg-grey-lighter">

@yield('body')

<script src="{{ mix('js/app.js') }}"></script>

@stack('scripts')

</body>

</html>