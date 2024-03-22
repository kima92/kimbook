<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == "he" ? "rtl" : "ltr" }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="גלו עולם של דמיון עם הפלטפורמה שלנו לסיפורים לילדים! סיפורים משעשעים, מחנכים ואינטראקטיביים מחכים להדליק דמיון ושמחה בלב הילדים. מושלם להורים ומחנכים שרוצים לעורר אהבה לקריאה ולסיפורים. התחילו את ההרפתקה שלכם היום!">
        <meta name=”robots” content="index, follow">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Open%20Sans&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Secular+One&display=swap" rel="stylesheet">
        <link href="/css/book.css" rel="stylesheet">

        <script type="text/javascript" src="/js/turnjs4/all.js"></script>
        <script type="text/javascript" src="/js/turnjs4/hash.js"></script>
        <script type="text/javascript" src="/js/turnjs4/turn.min.js"></script>
        <script type="text/javascript" src="/js/turnjs4/zoom.min.js"></script>

        {{ \App\Utils\Vite::compile() }}
    </head>
    <body class="antialiased">
    <!-- Section: Design Block -->
    @php($welcomeBooks = \App\Models\Book::whereIn('uuid', config('welcome.book'))->inRandomOrder()->get())
    <section>
        <!-- Jumbotron -->
        @php($bgPaths = Arr::random(['a3113bb6-6047-4b10-9d1c-ac31c490f237.webp', 'bg2.png', 'dogs.webp', 'cute.webp'], 2))
        <div class="relative bg-fixed bg-cover bg-no-repeat lg:h-screen" style="min-height: 100%;background-position: 50%; background-image: url('/images/{{ $bgPaths[0] }}');">
            <div class="h-full w-full overflow-hidden bg-[hsla(0,100%,100%,0.85)] dark:bg-[hsla(0,0%,0%,0.90)]">
                <div class="flex h-full items-center justify-center">
                    <div class="lg:p-6 p-2 text-center dark:text-white md:px-12 lg:w-4/5 w-5/6 rounded-3xl lg:my-4">
                        <x-dark-mode></x-dark-mode>

                        <h1 class="self-center bg-gradient-to-br from-purple-400 to-pink-500 dark:to-[rgba(122,90,248,1)] bg-clip-text text-6xl md:text-8xl font-semibold text-transparent">
                            {{ __("Craft Tales with Your Little Storyteller") }}
                        </h1>

                        <h2 class="text-2xl">ברוכים הבאים ל<span class="text-purple-500 font-bold">{{ config("app.name") }}</span>
                            <x-application-logo class="h-10 w-auto fill-current text-purple-400 inline" />
                        </h2>
                        <h2 class="text-xl">
                            {{ __("Dive into a world of imagination where you and your child collaborate together to create tales that are uniquely yours, complete with beautiful artwork.") }}
                        </h2>

                        <div class="flex lg:flex-col flex-col-reverse">
                            <div class="lg:flex flex-col mt-4 text-start text-lg gap-4">
                                <div>
                                    <div class="grid lg:grid-cols-3 grid-cols-1 gap-5 lg:gap-6 lg:mt-16 mt-10 justify-items-center lg:w-3/4 mx-auto">
                                        @foreach([
        ["text" => __("Customized books for with unlimited ideas"), "color" => "", "image" => "/images/endless-options.png"],
        ["text" => __("Seeking Fun or a Lesson? Let Us Assist!")  , "color" => "", "image" => "/images/learn-or-fun.png"],
        ["text" => __("Super lazy? Explore stories of other kids"), "color" => "", "image" => "/images/sloth.png"]
        ] as $data)
                                            <div class="
                                         relative w-60 h-60 bg-pink-200 flex justify-center items-center2 rounded-full p-5
                                         before:absolute before:inset-0 before:bg-[linear-gradient(315deg,#03a9f4,#8055a6,#c0005e)] before:rounded-[2rem]
                                         after:absolute   after:inset-0  after:bg-[linear-gradient(315deg,#03a9f4,#8055a6,#c0005e)]  after:blur-[30px]
                                         ">
                                                <b class="z-10 rounded-[2rem] absolute inset-1.5 bg-white/90 dark:bg-black/90"></b>
                                                @if($data["image"])<img class="absolute z-20 w-2/3 h-2/3 top-4" src="{{ $data["image"] }}">@endif
                                                <div class="p-4 z-20 absolute flex flex-col items-center bottom-4">
                                                    <p class="dark:text-white text-blue-600 text-center font-extrabold leading-none uppercase">{{ $data["text"] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="lg:w-2/3">
                                    {{--                                <x-book :pages="$welcomeBooks->first()->toBookArray()"></x-book>--}}
                                </div>
                            </div>

                            <div class="flex lg:flex-row flex-col lg:gap-4 justify-center">
                                <form action="/dashboard" method="get">
                                    <button type="submit" class="mt-4 bg-purple-900 rounded-3xl px-[46px] pt-[14px] pb-[12px] text-sm font-medium uppercase leading-normal text-purple-100 transition duration-150 ease-in-out hover:border-purple-700 hover:bg-purple-700 2hover:bg-opacity-10 hover:text-purple-100 focus:border-purple-700 focus:text-purple-100 focus:outline-none focus:ring-0 active:border-purple-600 active:text-purple-100">
                                        {{ __("Begin Your Adventure") }}
                                    </button>
                                </form>
                                @guest
                                    <form action="/register" method="get">
                                        <button type="submit" class="mt-4 bg-purple-900 rounded-3xl px-[46px] pt-[14px] pb-[12px] text-sm font-medium uppercase leading-normal text-purple-100 transition duration-150 ease-in-out hover:border-purple-700 hover:bg-purple-700 2hover:bg-opacity-10 hover:text-purple-100 focus:border-purple-700 focus:text-purple-100 focus:outline-none focus:ring-0 active:border-purple-600 active:text-purple-100">
                                            {{ __("Register") }}
                                        </button>
                                    </form>
                                @endguest
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- Jumbotron -->
        <div class="relative bg-fixed bg-cover bg-no-repeat lg:h-screen" style="min-height: 100%;background-position: 50%; background-image: url('/images/{{ $bgPaths[1] }}');">
            <div class="h-full w-full overflow-hidden bg-[hsla(0,100%,100%,0.85)] dark:bg-[hsla(0,0%,0%,0.90)]">
                <div class="mb-14">
                    <x-books-grid :books="$welcomeBooks->splice(1)" />
                </div>
                <div class="absolute bottom-3 w-full text-center font-extrabold text-purple-500 mt-10">
                    {{ config("app.name") }} - {{ config("mail.mailers.smtp.username") }}
                </div>
            </div>

        </div>
        <!-- Jumbotron -->
    </section>
    <!-- Section: Design Block -->
    </body>
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

    @stack('scripts')

    <script src="/js/nagishli_v3_beta/nagishli_beta.js?v=3.0b" charset="utf-8" defer></script>
</html>
