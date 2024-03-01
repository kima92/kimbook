@props(['bgclass' => ''])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == "he" ? "rtl" : "ltr" }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script src="https://unpkg.com/htmx.org@1.9.10" integrity="sha384-D1Kt99CQMDuVetoL1lrYwg5t+9QdHe7NLX/SoJYkXDFfX37iInKRy5xLSi8nO7UC" crossorigin="anonymous"></script>
        <script type="text/javascript" src="/js/turnjs4/all.js"></script>
        <script type="text/javascript" src="/js/turnjs4/hash.js"></script>
        <script type="text/javascript" src="/js/turnjs4/turn.min.js"></script>
        <script type="text/javascript" src="/js/turnjs4/zoom.min.js"></script>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Secular+One&display=swap" rel="stylesheet">
        <link href="/css/book.css" rel="stylesheet">

        {{ \App\Utils\Vite::compile() }}
    </head>
    <body class="font-sans antialiased" style="background-color: #0b1012">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main
                @if($bgclass ?? '') class="relative overflow-hidden2 bg-cover bg-no-repeat min-h-screen {{ $bgclass }}"
                @else class="relative overflow-hidden2 bg-cover bg-no-repeat min-h-screen" style="background-position: 50%; background-image: url('/images/dashboard.webp')"> @endif
                <div class="absolute top-0 right-0 bottom-0 left-0 h-full w-full bg-[hsla(0,0%,0%,0.25)] bg-fixed">
                    <div class="flex h-full justify-center">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-RKGVRPNJWV"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'G-RKGVRPNJWV');
        </script>
    </body>
</html>
