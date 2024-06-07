<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title> @yield('title') </title>
        <link rel="apple-touch-icon" sizes="57x57" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ secure_asset('assets/brand/logo.jpg')}}">
        <link rel="manifest" href="{{ secure_asset('assets/favicon/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ secure_asset('assets/brand/logo.jpg') }}">
        <meta name="theme-color" content="#ffffff">
        <!-- Vendors styles-->

        <link rel="stylesheet" href="{{ secure_asset('css/vendors/simplebar.css') }}">
        <link rel="stylesheet" href="{{ secure_asset('css/simplebar.css') }}">
        <!-- Main styles for this application-->
        <link href="{{ secure_asset('css/style.css')}}" rel="stylesheet">
        <link href="{{ secure_asset('css/examples.css')}}" rel="stylesheet">
        <script src="{{ secure_asset('js/config.js')}}"></script>
        <script src="{{ secure_asset('js/color-modes.js')}}"></script>
        <link href="{{ secure_asset('css/coreui-chartjs.css') }}" rel="stylesheet">
        <link rel="canonical" href="https://coreui.io/docs/components/modal/">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        @yield('css')
        <style>
            .modal {
                opacity: 100;
                background-color: rgba(0, 0, 0, 0.5);
                padding: 10%;
            }
        </style>
    </head>
    <body>
        <div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
            @include('layouts.sidebar')
        </div>
        <div class="wrapper d-flex flex-column min-vh-100">
            <header class="header header-sticky p-0 mb-4">
                @include('layouts.header')
            </header>
            @yield('content')
            @include('layouts.footer')
        </div>

        <!-- CoreUI and necessary plugins-->
        <script src="{{ secure_asset('js/coreui.bundle.min.js') }}"></script>
        <script src="{{ secure_asset('js/simplebar.min.js') }}"></script>
        <script src="{{ secure_asset('js/popovers.js' )}}"></script>
        <script src="js/tooltips.js"></script>
        <script>
        const header = document.querySelector('header.header');

        document.addEventListener('scroll', () => {
            if (header) {
            header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
            }
        });

        </script>
        <!-- Plugins and scripts required by this view-->
        <script src="{{ secure_asset('js/chart.umd.js') }}"></script>
        <script src="{{ secure_asset('js/coreui-chartjs.js') }}"></script>
        <script src="{{ secure_asset('js/index.js') }}"></script>
        <script src="{{ secure_asset('js/main.js')}}"></script>

        @yield('script')
        <script>
            @if(Session::has('success'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.success("{{ session('success') }}");
            @endif

            @if(Session::has('error'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.error("{{ session('error') }}");
            @endif

            @if(Session::has('info'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.info("{{ session('info') }}");
            @endif

            @if(Session::has('warning'))
            toastr.options =
            {
                "closeButton" : true,
                "progressBar" : true
            }
                    toastr.warning("{{ session('warning') }}");
            @endif
        </script>
    </body>
</html>
