@props(['pages', 'height' => 500, 'textColor' => 'white'])
<div id="{{ $id = Str::random(8) }}" {{ $attributes->merge(['class' => 'sample-flipbook hidden lg:block']) }}>
    @foreach ((array) $pages as $i => $page)
        <div @class(['hard' => $page["isCover"] ?? false])>
            @if($page["image"] ?? null)<div class="relative overflow-hidden bg-cover bg-no-repeat" style="height: {{ $height }}px;
                                                                                                          padding: 30px;
                                                                                                          background-image: url('{{$page["image"]}}');
                                                                                                          ">@endif
                @if($page["title"] ?? null)<div><h3 class="text-center text-5xl leading-tight" style="font-family: 'Secular One', sans-serif;direction: rtl; -webkit-text-stroke-color: #FFFFFF; -webkit-text-stroke-width: 1px">{{ $page["title"] }}</h3></div>@endif
                <div class="text-2xl h-full flex flex-col gap-2 justify-center justify-items-center " style="direction: rtl; padding: 30px 30px 0 30px; height: 460px">
                    @foreach(explode("\n", $page["content"] ?? "") as $p)
                        <p @if($page["image"] ?? null)style="-webkit-text-stroke-color: #FFFFFF; -webkit-text-stroke-width: thin" class="text-4xl" @endif>{{ $p }}</p>
                    @endforeach
                </div>
                @if($page["pageNum"] ?? null)<div class="text-center text-gray-400">-{{$page["pageNum"]}}-</div>@endif
            @if($page["image"] ?? null)</div>@endif
            @if($page["isCover"] ?? false)
                <div class="w-[calc(100%-2rem)] h-full absolute top-0 @if($i == 0) left-0 @else right-0 @endif bottom-0" style="box-shadow: 0 1.1px 1.5px rgba(0,0,0,.4), 0 2.8px 3.9px rgba(0,0,0,.4), 0 5.8px 7.9px rgba(0,0,0,.08), 0 12.0455px 16.4px rgba(0,0,0,.4), 0 33px 45px rgba(0,0,0,.8);"></div>
            @endif
        </div>
    @endforeach
</div>
<style>

    .carousel-container {
        margin: auto;
        position: relative;
        display: flex;
        flex-direction: column;
        gap: var(--lx-gap);

        .carousel {
            /*aspect-ratio: 16/9;*/
            width: 100%;
            position: relative;
            overflow: hidden;

            .item {
                opacity: 0;
                width: 100%;
                height: 100%;
                display: none;
                transition: opacity 0.5s ease-in-out;

                img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    object-position: center;
                }

                .caption {
                    width: 100%;
                    padding: var(--lx-space-01);
                    position: absolute;
                    bottom: 0;
                    text-transform: uppercase;
                    text-align: center;
                    font-size: 12px;
                    background-color: rgba(0, 0, 0, 0.5);
                }

                &.active {
                    opacity: 1;
                    display: block;
                }
            }
        }

        .dots {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;

            .dot {
                cursor: pointer;
                height: 10px;
                width: 10px;
                background-color: #242421;
                transition: background-color 0.2s ease;

                &.active,
                &:hover {
                    background-color: #ffffe6;
                }
            }
        }
    }

</style>

<div class="lg:hidden">
    <div class="carousel-container">
        <div class="carousel">
        @foreach ((array) $pages as $i => $page)
            @if($page["isCover"] ?? null || ($i % 2 === 1))<div @class(["item", "active" => $i == 0])>@endif
                @if($page["title"] ?? null)<div><h3 class="text-center bg-[hsla(0,0%,100%,0.70)] leading-6" style="direction: rtl; ">{{ $page["title"] }}</h3></div>@endif
                @if($page["image"] ?? null)<img src="{{$page["image"]}}" />@endif
                <div class="my-4" style="direction: rtl">
                    @foreach(explode("\n", $page["content"] ?? "") as $p)
                        <p class="mb-2 text-{{ $textColor }}">{{ $p }}</p>
                    @endforeach
                </div>
            @if($page["isCover"] ?? null || ($i % 2 === 0))</div>@endif
        @endforeach
        </div>
        <div class="flex gap-x-4 justify-center">
            <button class="prev text-{{ $textColor }} text-2xl"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </button>
            <div class="dots"></div>
            <button class="next text-{{ $textColor }} text-2xl"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 9-3 3m0 0 3 3m-3-3h7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg></button>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        let carousel = document.querySelector(".carousel");
        let items = carousel.querySelectorAll(".item");
        let dotsContainer = document.querySelector(".dots");

        // Insert dots into the DOM
        items.forEach((_, index) => {
            let dot = document.createElement("span");
            dot.classList.add("dot");
            if (index === 0) dot.classList.add("active");
            dot.dataset.index = index;
            dotsContainer.appendChild(dot);
        });

        let dots = document.querySelectorAll(".dot");

        // Function to show a specific item
        function showItem(index) {
            items.forEach((item, idx) => {
                item.classList.remove("active");
                dots[idx].classList.remove("active");
                if (idx === index) {
                    item.classList.add("active");
                    dots[idx].classList.add("active");
                }
            });
        }

        // Event listeners for buttons
        document.querySelector(".prev").addEventListener("click", () => {
            let index = [...items].findIndex((item) =>
                item.classList.contains("active")
            );
            showItem((index - 1 + items.length) % items.length);
        });

        document.querySelector(".next").addEventListener("click", () => {
            let index = [...items].findIndex((item) =>
                item.classList.contains("active")
            );
            showItem((index + 1) % items.length);
        });

        // Event listeners for dots
        dots.forEach((dot) => {
            dot.addEventListener("click", () => {
                let index = parseInt(dot.dataset.index);
                showItem(index);
            });
        });
    });

</script>
<script type="text/javascript">
    $("#{{ $id }}").turn({
        width: {{ $height * 2 }},
        height: {{ $height }},
        autoCenter: true,
        // direction: "rtl",
        // dir: "rtl",
    });
</script>
