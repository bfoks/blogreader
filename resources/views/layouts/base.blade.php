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

    <meta property="og:title" content="Blogreader.pro - read & manage your favorite blogs">
    <meta property="og:description" content="Read every blog in a convenient way">
    <meta property="og:url" content="https://blogreader.pro">
    <meta property="og:image" content="https://blogreader.pro/images/og_image.png">
    <meta property="og:image:width" content="650">
    <meta property="og:image:height" content="320">

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