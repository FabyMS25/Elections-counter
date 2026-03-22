<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      data-topbar="light" >
<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ URL::asset('build/images/logo_elections.png')}}">
    @include('layouts.head-css')
</head>
@yield('body')
    <div id="layout-wrapper">
        @yield('content')        
    </div>
    @include('layouts.vendor-scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @yield('script')
</body>
</html>
