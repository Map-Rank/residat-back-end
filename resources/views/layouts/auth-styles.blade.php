<!DOCTYPE html>
<html lang="en">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="Map&Rank - Residat">
    <meta name="author" content="Map&Rank">
    <meta name="keyword" content="Map&Rank, Residat, Wheather,">
    <title>Map&Rank - Residat</title>
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/brand/logo.jpg')}}">
    <link rel="manifest" href="{{ asset('assets/favicon/manifest.json')}}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('assets/brand/logo.jpg')}}">
    <meta name="theme-color" content="#ffffff">
    <!-- Vendors styles-->
    <link rel="stylesheet" href="{{ asset('node_modules/simplebar/dist/simplebar.css')}}">
    {{-- @import "~node_modules/simplebar/dist/simplebar.css"; --}}
    {{-- @import'~node_modules/@coreui/icons/sprites/free.svg#cil-user --}}
    <link rel="stylesheet" href="{{ asset('css/vendors/simplebar.css')}}">
    <!-- Main styles for this application-->
    <link href="{{ asset('css/style.css')}}" rel="stylesheet">
    <link href="{{ asset('css/examples.css')}}" rel="stylesheet">
    <script src="{{ asset('js/config.js')}}"></script>
    <script src="{{ asset('js/color-modes.js')}}"></script>
</head>

<body>
    @yield('auth-content')
    <script>
        const header = document.querySelector('header.header');

        document.addEventListener('scroll', () => {
            if (header) {
                header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
            }
        });
    </script>
    <script></script>
</body>

</html>
