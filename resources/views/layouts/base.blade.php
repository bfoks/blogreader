<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>blogreader.pro beta</title>

    <style>
        html, body {
            height: 100%;
            min-height: 100%;
            font-family: 'Nunito', sans-serif;
        }

        * {
            /*outline: 1px solid red;*/
        }
    </style>

    @stack('styles')

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

</head>

<body class="bg-grey-lightest">

    @yield('body')

    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')

</body>

</html>