@props(['books' => collect()])

<div class="grid lg:grid-cols-3 lg:gap-y-8 gap-y-2 gap-x-16 mt-24 w-11/12 mx-auto">
    @php
    /** @var \App\Models\Book $book */
    @endphp
    @foreach($books->filter(fn(\App\Models\Book $book) => $book->chapters->first()?->images?->first()?->image_url)->reverse() as $book)
        <a href="/books/{{$book->uuid}}">
            <div class="p-4 bg-[hsla(0,0%,0%,0.70)] text-black rounded-3xl transform transition duration-500 hover:scale-110">
                <div class="overflow-hidden bg-contain bg-center bg-no-repeat lg:p-30 hidden lg:block"
                     style="background-image: url('{{$book->chapters->first()->images->first()->image_url}}');height: 450px;">
                    <h3 class="text-center bg-[hsla(0,0%,100%,0.70)] leading-6 mx-auto content" style="direction: rtl;">
                        {{ $book->title }}
                    </h3>
                </div>

                <div class="block lg:hidden">
                    <div><h3 class="text-center bg-[hsla(0,0%,100%,0.70)] leading-6" style="direction: rtl; ">{{ $book->title }}</h3></div>
                    <img src="{{$book->chapters->first()->images->first()->image_url}}" />
                </div>
            </div>
        </a>
    @endforeach
</div>
