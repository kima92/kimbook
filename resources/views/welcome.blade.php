<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == "he" ? "rtl" : "ltr" }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Open%20Sans&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Secular+One&display=swap" rel="stylesheet">
        <link href="/css/book.css" rel="stylesheet">

        <script src="https://unpkg.com/htmx.org@1.9.10" integrity="sha384-D1Kt99CQMDuVetoL1lrYwg5t+9QdHe7NLX/SoJYkXDFfX37iInKRy5xLSi8nO7UC" crossorigin="anonymous"></script>

        <script type="text/javascript" src="/js/turnjs4/all.js"></script>
        <script type="text/javascript" src="/js/turnjs4/hash.js"></script>
        <script type="text/javascript" src="/js/turnjs4/turn.min.js"></script>
        <script type="text/javascript" src="/js/turnjs4/zoom.min.js"></script>

        {{ \App\Utils\Vite::compile() }}
    </head>
    <body class="antialiased">
    <!-- Section: Design Block -->
    <section>
        <!-- Jumbotron -->
        <div class="relative overflow-hidden bg-cover bg-no-repeat lg:h-screen" style="min-height: 100%;background-position: 50%; background-image: url('/images/bg2.png');">
            <div class="h-full w-full overflow-hidden bg-[hsla(0,0%,0%,0.25)]">
                <div class="flex h-full items-center justify-center">
                    <div class="lg:p-6 p-2 text-center text-purple-900 md:px-12 lg:w-3/4 w-5/6 bg-[hsla(0,0%,100%,0.90)] rounded-3xl my-4">
                        <h1 class="mt-2 mb-16 text-5xl font-bold text-purple-400 tracking-tight md:text-6xl xl:text-7xl">
                            {{ __("Craft Tales with Your Little Storyteller") }}
                        </h1>

                        <h2 class="text-xl">
                            {{ __("Dive into a world of imagination where you and your child collaborate together to create tales that are uniquely yours, complete with beautiful artwork.") }}
                        </h2>

                        <div class="lg:flex mt-4 text-start text-lg">
                            <div class="hidden lg:block">
                                <ul class="list-disc mt-16">
                                    <li>{{ __("Customized books for with unlimited ideas") }}</li>
                                    <li>{{ __("Seeking Fun or a Lesson? Let Us Assist!") }}</li>
                                    <li>{{ __("Super lazy? Explore stories of other kids") }}</li>
                                </ul>
                            </div>
                            <div class="lg:w-2/3 lg:mt-0 mt-8">
                                <x-book :textColor="'purple-900'" :pages="\App\Models\Book::firstWhere('uuid', \Illuminate\Support\Arr::random(config('welcome.book')))->toBookArray()"></x-book>
                            </div>
                        </div>
                        <form action="/dashboard" method="get">
                            <button type="submit" class="mt-4 rounded border-2 border-purple-900 px-[46px] pt-[14px] pb-[12px] text-sm font-medium uppercase leading-normal text-purple-900 transition duration-150 ease-in-out hover:border-purple-700 hover:bg-purple-700 hover:bg-opacity-10 hover:text-purple-700 focus:border-purple-700 focus:text-purple-700 focus:outline-none focus:ring-0 active:border-purple-600 active:text-purple-600">
                                {{ __("Begin Your Adventure") }}
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- Jumbotron -->
    </section>
    <!-- Section: Design Block -->
    </body>
</html>
