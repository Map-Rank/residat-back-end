<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title> @yield('title') </title>
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/favicon/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/favicon/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/favicon/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/favicon/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/favicon/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/favicon/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/favicon/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/favicon/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/favicon/android-icon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('assets/favicon/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('assets/favicon/ms-icon-144x144.png') }}">
        <meta name="theme-color" content="#ffffff">
        <!-- Vendors styles-->
        <link rel="stylesheet" href="{{ mix('node_modules/simplebar/dist/simplebar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/vendors/simplebar.css') }}">
        <!-- Main styles for this application-->
        <link href="{{ asset('css/style.css')}}" rel="stylesheet">
        <link href="{{ asset('css/examples.css')}}" rel="stylesheet">
        <script src="{{ asset('js/config.js')}}"></script>
        <script src="{{ asset('js/color-modes.js')}}"></script>
        {{-- <link href="node_modules/@coreui/chartjs/dist/css/coreui-chartjs.css" rel="stylesheet"> --}}
        <link rel="stylesheet" href="{{ mix('node_modules/@coreui/chartjs/dist/css/coreui-chartjs.css') }}">
        {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet"> --}}
	
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	      <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        @yield('css')
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
    <script src="{{ mix('node_modules/@coreui/coreui/dist/js/coreui.bundle.min.js')}}"></script>
    <script src="{{ mix('node_modules/simplebar/dist/simplebar.min.js')}}"></script>
    <script>
      const header = document.querySelector('header.header');
      
      document.addEventListener('scroll', () => {
        if (header) {
          header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
        }
      });
      
    </script>
    <!-- Plugins and scripts required by this view-->
    <script src="{{ mix('node_modules/chart.js/dist/chart.umd.js')}}"></script>
    <script src="{{ mix('node_modules/@coreui/chartjs/dist/js/coreui-chartjs.js')}}"></script>
    <script src="{{ mix('node_modules/@coreui/utils/dist/umd/index.js')}}"></script>
    <script src="{{ asset('js/main.js')}}"></script>
    <script> 
    </script>
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
    {{-- @yield('script') --}}
    </body>
</html>
