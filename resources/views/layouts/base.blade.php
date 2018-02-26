<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="styles.css">
    <title>blogreader.pro beta</title>

    {{--<link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">--}}

    <style>
        html, body {
            height: 100%;
        }
    </style>

    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

</head>

<body class="font-sans bg-grey-lighter">
    @yield('body')

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>