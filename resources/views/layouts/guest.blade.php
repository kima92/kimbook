<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == "he" ? "rtl" : "ltr" }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->

        {{ \App\Utils\Vite::compile() }}
        {!! RecaptchaV3::initJs() !!}
          </head>
    <body class="font-sans text-gray-900 antialiased">
    <section>
        <!-- Jumbotron -->
        @php($bgPaths = Arr::random(['a3113bb6-6047-4b10-9d1c-ac31c490f237.webp', 'bg2.png', 'dogs.webp', 'cute.webp'], 2))
        <div class="relative bg-fixed bg-cover bg-no-repeat lg:h-screen" style="min-height: 100%;background-position: 50%; background-image: url('/images/{{ $bgPaths[0] }}');">
            <div class="h-full w-full overflow-hidden bg-[hsla(0,100%,100%,0.85)] dark:bg-[hsla(0,0%,0%,0.90)]">
                <div class="flex items-center justify-center">
                    <div class="lg:p-6 p-2 text-center dark:text-white md:px-12 lg:w-4/5 w-5/6 rounded-3xl lg:my-4">
                        <x-dark-mode></x-dark-mode>
                        <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
                            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-transparent overflow-hidden">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </body>
</html>
