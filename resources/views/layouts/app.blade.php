<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-pt-20">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'EyeTech — Phones, accessories & trusted repair')</title>
    <meta name="description" content="@yield('meta_description', 'EyeTech sells mobile phones, accessories and offers trusted same-day repair, unlocking and software services.')">

    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <meta property="og:title" content="@yield('title', 'EyeTech')">
    <meta property="og:description" content="@yield('meta_description', 'Phones, accessories & trusted repair.')">
    <meta property="og:type" content="website">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('website.partials.nav')

    <main>
        @yield('content')
    </main>

    @include('website.partials.footer')
</body>
</html>
