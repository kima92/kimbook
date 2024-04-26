{{--<x-app-layout :bgclass="'bg-gradient-to-b from-slate-900 to-slate-700'">--}}
<x-app-layout>
@php
    /** @var \App\Models\Book $book **/
@endphp

    <div class="flex flex-col w-11/12">
        <div class="lg:flex lg:flex-row mt-4 gap-4 mx-auto">
            <div class="lg:w-3/5 p-4 dark:bg-[hsla(0,0%,0%,0.70)] bg-[hsla(0,0%,100%,0.50)] dark:text-white rounded-3xl relative lg:max-w-md">
                <h1 class="self-center bg-gradient-to-br from-purple-400 to-pink-500 dark:to-[rgba(122,90,248,1)] bg-clip-text text-4xl font-semibold text-transparent mb-4">
                    {{ $book->title }}</h1>
                <h2 class="mt-3 text-lg">{{ $book->user->name }}</h2>
                <div class="mt-3 flex flex-row">
                    <svg width="22px" height="22px" viewBox="0 -4 20 20" class="mx-1">
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="Dribbble-Light-Preview" transform="translate(-260.000000, -4563.000000)" fill="#ffffff">
                                <g id="icons" transform="translate(56.000000, 160.000000)">
                                    <path d="M216,4409.00052 C216,4410.14768 215.105,4411.07682 214,4411.07682 C212.895,4411.07682 212,4410.14768 212,4409.00052 C212,4407.85336 212.895,4406.92421 214,4406.92421 C215.105,4406.92421 216,4407.85336 216,4409.00052 M214,4412.9237 C211.011,4412.9237 208.195,4411.44744 206.399,4409.00052 C208.195,4406.55359 211.011,4405.0763 214,4405.0763 C216.989,4405.0763 219.805,4406.55359 221.601,4409.00052 C219.805,4411.44744 216.989,4412.9237 214,4412.9237 M214,4403 C209.724,4403 205.999,4405.41682 204,4409.00052 C205.999,4412.58422 209.724,4415 214,4415 C218.276,4415 222.001,4412.58422 224,4409.00052 C222.001,4405.41682 218.276,4403 214,4403">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </svg>
                    {{ $book->readings()->count() }}
                </div>
                <div class="mt-3 flex flex-row">
                    <a target="_blank" href="{{url("/books/{$book->uuid}/print")}}">
                        <svg fill="#ffffff" height="25px" width="25px" class="mx-1 inline"
                             viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
                            <g id="Printer">
                                <path d="M57.7881012,14.03125H52.5v-8.0625c0-2.2091999-1.7909012-4-4-4h-33c-2.2091999,0-4,1.7908001-4,4v8.0625H6.2119002
                            		C2.7871001,14.03125,0,16.8183498,0,20.2431507V46.513649c0,3.4248009,2.7871001,6.2119026,6.2119002,6.2119026h2.3798995
                            		c0.5527,0,1-0.4472008,1-1c0-0.5527-0.4473-1-1-1H6.2119002C3.8896,50.7255516,2,48.8359489,2,46.513649V20.2431507
                            		c0-2.3223,1.8896-4.2119007,4.2119002-4.2119007h51.5762024C60.1102982,16.03125,62,17.9208508,62,20.2431507V46.513649
                            		c0,2.3223-1.8897018,4.2119026-4.2118988,4.2119026H56c-0.5527992,0-1,0.4473-1,1c0,0.5527992,0.4472008,1,1,1h1.7881012
                            		C61.2128983,52.7255516,64,49.9384499,64,46.513649V20.2431507C64,16.8183498,61.2128983,14.03125,57.7881012,14.03125z
                            		 M13.5,5.96875c0-1.1027999,0.8971996-2,2-2h33c1.1027985,0,2,0.8972001,2,2v8h-37V5.96875z"/>
                                <path d="M44,45.0322495H20c-0.5517998,0-0.9990005,0.4472008-0.9990005,0.9990005S19.4482002,47.0302505,20,47.0302505h24
                            		c0.5517006,0,0.9990005-0.4472008,0.9990005-0.9990005S44.5517006,45.0322495,44,45.0322495z"/>
                                <path d="M44,52.0322495H20c-0.5517998,0-0.9990005,0.4472008-0.9990005,0.9990005S19.4482002,54.0302505,20,54.0302505h24
                            		c0.5517006,0,0.9990005-0.4472008,0.9990005-0.9990005S44.5517006,52.0322495,44,52.0322495z"/>
                                <circle cx="7.9590998" cy="21.8405495" r="2"/>
                                <circle cx="14.2856998" cy="21.8405495" r="2"/>
                                <circle cx="20.6121998" cy="21.8405495" r="2"/>
                                <path d="M11,62.03125h42v-26H11V62.03125z M13.4036999,38.4349518h37.1925964v21.1925964H13.4036999V38.4349518z"/>
                            </g>
                        </svg>
                        גרסא להדפסה
                    </a>
                </div>
                <p class="mt-3">{{ $book->description }}</p>
                <p class="mt-3 text-gray-500">{{ $book->input }}</p>
                <p class="mt-3">{{ $book->publication_date->format("d/m/Y") }}</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    @php($colors = ["cyan", "yellow", "rose", "blue", "green", "orange", "pink"])
                    @foreach(explode(",", $book->tags) as $tag)
                        @php($color = array_pop($colors))
                        <div class="rounded-3xl border-2 border-{{ $color }}-500 text-{{ $color }}-500 px-2 py-1  bg-[hsla(0,0%,100%,0.15)]">{{ trim($tag) }}</div>
                    @endforeach
                </div>
{{--                <p>דירוג: {{ $book->rating }}</p>--}}

                <div class="lg:absolute bottom-6 mt-6 flex-row">
                    <a href="/books/{{$book->uuid}}/next" class="rounded-3xl border-0 mx-2 border-white lg:px-6 px-2 pt-[8px] pb-[8px] text-sm font-medium leading-normal bg-blue-600 text-white transition duration-150 ease-in-out hover:border-blue-200 hover:bg-blue-700 hover:text-blue-200 focus:border-blue-300 focus:text-blue-200 focus:outline-none focus:ring-0 active:border-blue-300 active:text-blue-300">{{ __("Next Story") }}</a>
                    <a href="/dashboard" class="rounded-3xl border-0 border-white mx-2 lg:px-6 px-2  pt-[8px] pb-[8px] text-sm font-medium leading-normal bg-violet-600 text-white transition duration-150 ease-in-out hover:border-blue-200 hover:bg-violet-700 hover:text-violet-200 focus:border-violet-300 focus:text-violet-200 focus:outline-none focus:ring-0 active:border-violet-300 active:text-violet-300">{{ __("Write New Story") }}</a>
                </div>
            </div>

            <div class="p-4 lg:p-6 rounded-3xl text-right w-full mt-2">
{{--            <div class="mx-auto2 p-6 bg-[hsla(0,0%,0%,0.70)] rounded-3xl text-right" style="width: 1040px">--}}
                @if($canView)
                    <x-book :pages="$book->toBookArray()" :ratio="$book->additional_data['artModel'] ?? null == \App\AI\Art\ReplicateInstantId::class ? 1.65 : 2"></x-book>
                @else
                    <div class="relative overflow-hidden bg-contain bg-no-repeat bg-center" style="height: 500px;padding: 30px;background-image: url('{{ $book->chapters->first()->images->first()->image_url }}');">
                        <div>
                            <h1 class="text-center bg-[hsla(0,0%,100%,0.70)] leading-6 mx-auto content text-2xl" style="width: 450px; direction: rtl;">
                                מכסת הצפיות נגמרה.
                            </h1>
                            <h1 class="text-center bg-[hsla(0,0%,100%,0.70)] leading-6 mx-auto content text-2xl mt-24" style="width: 450px; direction: rtl;">
                                ניתן לשלם על מינוי חדש או לחכות לסיפורים של מחר
                            </h1>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        @if($book->user_id == Auth::id())
            <div class="p-4 lg:p-6 rounded-3xl text-right w-full mt-2 flex flex-col dark:text-white">
                <h1 class="text-2xl leading-6 mb-4">משוב</h1>
                <span>נשמח לקבל משוב על הספר ולשפר את החוויה שלכם!</span>
                <textarea required minlength="5" id="review-content" cols="4" maxlength="500" class="mt-4 h-28 bg-[hsla(0,0%,100%,0.50)] dark:bg-[hsla(0,0%,100%,0.10)] dark:border-2 rounded dark:border-white  font-medium dark:text-white focus:border-white">{{$book->additional_data["userReview"] ?? ""}}</textarea>
                <x-button id="review-submit" onclick="submitReview()" class="mt-4 px-[46px] pt-[14px] pb-[12px] flex items-center justify-center">
                    {{ __("Submit") }}
                </x-button>
                <span id="review-message" class="hidden">תודה רבה!</span>
            </div>

            <script>

                function submitReview() {
                    document.getElementById('review-submit').disabled = true;
                    document.getElementById('review-message').classList.add("hidden");

                    // Create an object to populate with the form data
                    const data = {
                        review: document.getElementById('review-content').value
                    };
                    // Collect form data

                    // Use fetch to send the data as a POST request
                    fetch('/books/{{$book->uuid}}', {
                        method: 'PUT', // Specify the request method
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json', // Specify the content type
                        },
                        body: JSON.stringify(data), // Convert the JavaScript object to a JSON string
                    })
                        .then(response => {
                            if (!response.ok) { // Checks if the response status code is outside the 200-299 range
                                return response.json().then(errorData => {
                                    // Handle HTTP error with custom error message
                                    throw new Error(errorData.error_message || 'Unknown error');
                                });
                            }
                            return response.json(); // Parse the JSON response
                        })
                        .then(data => {
                            document.getElementById('review-message').classList.remove("hidden");
                        })
                        .catch((error) => {
                            console.error('Error:', error); // Handle errors
                            document.getElementById('review-submit').disabled = false;
                        });
                }

            </script>
        @endif
    </div>
</x-app-layout>
