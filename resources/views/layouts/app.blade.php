<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>@yield('title', 'Keripik iLiL — Berani Coba, Berani Ketagihan')</title>
    <meta name="description" content="@yield('meta_description', 'Keripik pisang premium dari Bandung. Cinematic landing page dengan glassmorphism, parallax, dan Three.js.')" />
    <meta name="keywords" content="keripik pisang, keripik pisang renyah, keripik iLiL, camilan bandung, keripik pisang coklat lumer, keripik pisang balado, keripik pisang tanpa pengawet, banana chips premium" />

    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}" />

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:title" content="@yield('title', 'Keripik iLiL — Berani Coba, Berani Ketagihan')" />
    <meta property="og:description" content="@yield('meta_description', 'Keripik pisang premium dari Bandung. Cinematic landing page dengan glassmorphism, parallax, dan Three.js.')" />
    <meta property="og:image" content="{{ asset('assets/products/pack-hero.png') }}" />
    <meta property="og:site_name" content="Keripik iLiL" />

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="{{ url()->current() }}" />
    <meta name="twitter:title" content="@yield('title', 'Keripik iLiL — Berani Coba, Berani Ketagihan')" />
    <meta name="twitter:description" content="@yield('meta_description', 'Keripik pisang premium dari Bandung. Cinematic landing page dengan glassmorphism, parallax, dan Three.js.')" />
    <meta name="twitter:image" content="{{ asset('assets/products/pack-hero.png') }}" />

    {{-- JSON-LD Schema Markup (Local Business / Organization) --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "LocalBusiness",
      "name": "Keripik iLiL",
      "image": "{{ asset('assets/products/pack-hero.png') }}",
      "@@id": "{{ url('/') }}",
      "url": "{{ url('/') }}",
      "telephone": "+6281234567890",
      "priceRange": "Rp 15.000 - Rp 25.000",
      "address": {
        "@@type": "PostalAddress",
        "streetAddress": "Jl. Keripik No. 12",
        "addressLocality": "Bandung",
        "addressRegion": "Jawa Barat",
        "addressCountry": "ID"
      },
      "description": "Produsen keripik pisang premium dari Bandung, dibuat dari pisang pilihan tanpa pengawet dengan berbagai varian rasa.",
      "sameAs": [
        "https://www.instagram.com/keripikilil",
        "https://www.tiktok.com/@keripikilil"
      ]
    }
    </script>

    {{-- Fonts: Outfit --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vite: Tailwind CSS + App JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    @yield('content')

    @stack('scripts')
</body>
</html>
