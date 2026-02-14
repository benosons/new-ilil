<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>@yield('title', 'Keripik iLiL — Berani Coba, Berani Ketagihan')</title>
    <meta name="description" content="@yield('meta_description', 'Keripik pisang premium dari Bandung. Cinematic landing page dengan glassmorphism, parallax, dan Three.js.')" />

    {{-- Vite: Tailwind CSS + App JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    @yield('content')

    @stack('scripts')
</body>
</html>
