@props(['books' => collect()])

<div class="grid lg:grid-cols-3 lg:gap-y-8 gap-y-2 gap-x-16 mt-24 w-11/12 mx-auto">
    @php
    /** @var \App\Models\Book $book */
    @endphp
    @foreach($books->filter(fn(\App\Models\Book $book) => $book->chapters->first()?->images?->first()?->image_url)->reverse() as $book)
        <a href="/books/{{$book->uuid}}" class=" flex items-center justify-center">
            <div class=" w-80 h-80 bg-pink-200 flex justify-center rounded-full p-5
                        before:absolute before:inset-0 before:bg-[linear-gradient(315deg,#03a9f4,#8055a6,#c0005e)] before:rounded-[2rem]
                        after:absolute   after:inset-0  after:bg-[linear-gradient(315deg,#03a9f4,#8055a6,#c0005e)]  after:blur-[30px]
                        text-black  transform transition duration-500 hover:scale-110">
                <b class="z-10 rounded-[2rem] absolute inset-1.5 overflow-hidden bg-contain bg-no-repeat lg:p-30 h-max"
                   style="background-image: url('{{$book->chapters->first()->images->first()->image_url}}');height: 307px"></b>
                <div class="z-20">
                    <h3 class="text-2xl text-center bg-[hsla(0,0%,100%,0.70)] leading-6 mx-auto content z-20 p-2 rounded-3xl" style="direction: rtl;">
                        {{ $book->title }}
                    </h3>
                </div>
            </div>
        </a>
    @endforeach
</div>
