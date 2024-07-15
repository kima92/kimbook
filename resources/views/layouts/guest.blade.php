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

        <script type="text/javascript">
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "m1krx3jy7p");
        </script>
        <style>
            .google-btn {
                display: inline-block;
                background: white;
                color: #444;
                width: 190px;
                height: 42px;
                border-radius: 5px;
                box-shadow: 0 3px 4px 0 rgba(0, 0, 0, .25);
                transition: all 0.2s ease-in-out;
                text-decoration: none;
                text-align: center;
                line-height: 42px;
                font-size: 16px;
            }

            .google-btn:hover {
                box-shadow: 0 0 6px #4285f4;
            }

            .google-icon-wrapper {
                position: absolute;
                margin-top: 1px;
                margin-left: 1px;
                width: 40px;
                height: 40px;
                border-radius: 2px;
                background-color: #fff;
            }

            .google-icon {
                position: absolute;
                margin-top: 11px;
                margin-left: 11px;
                width: 18px;
                height: 18px;
            }

            .btn-text {
                display: inline-block;
                vertical-align: middle;
                padding-left: 42px;
                padding-right: 42px;
                font-weight: bold;
                font-size: 14px;
            }
        </style>
{{--        {!! RecaptchaV3::initJs() !!}--}}
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
    @stack('scripts')
</html>
