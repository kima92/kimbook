@props(['bgclass' => ''])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == "he" ? "rtl" : "ltr" }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
                <header class="bg-white dark:bg-black shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                <section>
                    <!-- Jumbotron -->
                    @php($bgPaths = Arr::random(['a3113bb6-6047-4b10-9d1c-ac31c490f237.webp', 'bg2.png', 'dogs.webp', 'cute.webp'], 2))
                    <div class="relative bg-fixed bg-cover bg-no-repeat lg:h-screen" style="min-height: 100%;background-position: 50%; background-image: url('/images/{{ $bgPaths[0] }}');">
                        <div class="min-h-full w-full bg-[hsla(0,100%,100%,0.85)] dark:bg-[hsla(0,0%,0%,0.90)]">
                            <div class="flex justify-center">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </section>
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
        <script>
            nagishli_config = {
                // Plugin language, you can choose en for English, and he for Hebrew
                language: "{{ app()->getLocale() }}",
                // Currently, you can choose from Blue, Red, Green, Purple, Pink, Yellow, Gray, Orange, Brown, Turquoise and Black
                color: "blue"
            };
        </script>

        <script src="/js/nagishli_v3_beta/nagishli_beta.js?v=3.0b" charset="utf-8" defer></script>
        @stack('scripts')
    </body>
</html>
