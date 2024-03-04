<x-app-layout>
    <div class="flex flex-col justify-center items-center">
        <div class="lg:p-6 mt-10 text-center text-white md:px-12  lg:w-11/12 w-full bg-[hsla(0,0%,0%,0.70)] rounded-3xl">
            <h1 class="mt-2 mb-2 text-3xl font-bold tracking-tight md:text-2xl xl:text-3xl">
                {{ __("Choose A Plan") }}
            </h1>

            <div class="lg:py-10 flex lg:flex-row flex-col justify-center">
                <!-- Basic Card -->
                <div class="lg:w-96 lg:p-8 p-2 bg-gray-900 text-center rounded-3xl lg:pr-16 shadow-xl lg:border-4 border-2 border-white mx-2 lg:mx-0">
                    <h1 class="text-white font-semibold text-2xl">{{ __("Basic") }}</h1>
                    <p class="pt-2 tracking-wide">
                        <span class="text-gray-400 align-top">₪</span>
                        <span class="text-2xl font-semibold">{{ $plans['basic']["cost"] }}</span>
                        <span class="text-gray-400 font-medium">/</span>
                        <span class="text-2xl font-semibold">{{ $plans['basic']["amount"] }}</span>
                        <span class="text-gray-400 font-medium"> {{ __("Credits") }}</span>
                    </p>
                    <hr class="mt-4 border-1">
                    {{--                <div class="pt-8">--}}
                    {{--                    <p class="font-semibold text-gray-400 text-left">--}}
                    {{--                            <span class="material-icons align-middle">--}}
                    {{--                                done--}}
                    {{--                            </span>--}}
                    {{--                        <span class="pl-2">--}}
                    {{--                                Get started with <span class="text-black">messaging</span>--}}
                    {{--                            </span>--}}
                    {{--                    </p>--}}
                    {{--                    <p class="font-semibold text-gray-400 text-left pt-5">--}}
                    {{--                            <span class="material-icons align-middle">--}}
                    {{--                                done--}}
                    {{--                            </span>--}}
                    {{--                        <span class="pl-2">--}}
                    {{--                                Flexible <span class="text-black">team meetings</span>--}}
                    {{--                            </span>--}}
                    {{--                    </p>--}}
                    {{--                    <p class="font-semibold text-gray-400 text-left pt-5">--}}
                    {{--                            <span class="material-icons align-middle">--}}
                    {{--                                done--}}
                    {{--                            </span>--}}
                    {{--                        <span class="pl-2">--}}
                    {{--                                <span class="text-black">5 TB</span> cloud storage--}}
                    {{--                            </span>--}}
                    {{--                    </p>--}}
                    <a href="/payments?plan=basic" class=""><p class="w-full lg:py-4 py-2 bg-blue-600 mt-8 rounded-xl text-white font-medium">
                            {{ __("Choose Plan") }}
                        </p></a>
                    {{--                </div>--}}
                </div>
                <!-- StartUp Card -->
                <div class="lg:w-80 lg:p-8 p-2 bg-white text-center rounded-3xl text-white lg:border-4 border-2 shadow-xl border-white m-2 lg:m-0 transform lg:scale-125">
                    <h1 class="text-black font-semibold text-2xl">{{ __("Standard") }}</h1>
                    <p class="pt-2 tracking-wide">
                        <span class="text-gray-400 align-top">₪</span>
                        <span class="text-blue-600 text-3xl font-semibold">{{ $plans['standard']["cost"] }}</span>
                        <span class="text-gray-400 font-medium">/</span>
                        <span class="text-blue-600 text-3xl font-semibold">{{ $plans['standard']["amount"] }}</span>
                        <span class="text-gray-400 font-medium"> {{ __("Credits") }}</span>
                    </p>
                    <hr class="mt-4 border-1 border-gray-600">
                    {{--                <div class="pt-8">--}}
                    {{--                    <p class="font-semibold text-gray-400 text-left">--}}
                    {{--                            <span class="material-icons align-middle">--}}
                    {{--                                done--}}
                    {{--                            </span>--}}
                    {{--                        <span class="pl-2">--}}
                    {{--                                All features in <span class="text-white">Basic</span>--}}
                    {{--                            </span>--}}
                    {{--                    </p>--}}
                    {{--                    <p class="font-semibold text-gray-400 text-left pt-5">--}}
                    {{--                            <span class="material-icons align-middle">--}}
                    {{--                                done--}}
                    {{--                            </span>--}}
                    {{--                        <span class="pl-2">--}}
                    {{--                                Flexible <span class="text-white">call scheduling</span>--}}
                    {{--                            </span>--}}
                    {{--                    </p>--}}
                    {{--                    <p class="font-semibold text-gray-400 text-left pt-5">--}}
                    {{--                            <span class="material-icons align-middle">--}}
                    {{--                                done--}}
                    {{--                            </span>--}}
                    {{--                        <span class="pl-2">--}}
                    {{--                                <span class="text-white">15 TB</span> cloud storage--}}
                    {{--                            </span>--}}
                    {{--                    </p>--}}

                    <a href="/payments?plan=standard" class=""><p class="w-full lg:py-4 py-2 bg-blue-600 mt-8 rounded-xl text-white font-medium">
                            {{ __("Choose Plan") }}
                        </p></a>
                    {{--                </div>--}}
                    <div class="absolute lg:top-4 lg:left-4 left-2 top-2">
                        <p class="bg-blue-700 font-semibold lg:px-4 px-2 py-1 rounded-full uppercase text-xs">{{ __('Popular') }}</p>
                    </div>
                </div>
                <!-- Enterprise Card -->
                <div class="lg:w-96 lg:p-8 p-2 bg-gray-900 text-center rounded-3xl lg:pl-16 shadow-xl lg:border-4 border-2  border-white mx-2 lg:mx-0">
                    <h1 class="text-white font-semibold text-2xl">{{ __("Premium") }}</h1>
                    <p class="pt-2 tracking-wide">
                        <span class="text-gray-400 align-top">₪</span>
                        <span class="text-2xl font-semibold">{{ $plans['premium']["cost"] }}</span>
                        <span class="text-gray-400 font-medium">/</span>
                        <span class="text-2xl font-semibold">{{ $plans['premium']["amount"] }}</span>
                        <span class="text-gray-400 font-medium"> {{ __("Credits") }}</span>
                    </p>
                    <hr class="mt-4 border-1">
                    {{--                <div class="pt-8">--}}
                    {{--                    <p class="font-semibold text-gray-400 text-left">--}}
                    {{--                            <span class="material-icons align-middle">--}}
                    {{--                                done--}}
                    {{--                            </span>--}}
                    {{--                        <span class="pl-2">--}}
                    {{--                                All features in <span class="text-black">Startup</span>--}}
                    {{--                            </span>--}}
                    {{--                    </p>--}}
                    {{--                    <p class="font-semibold text-gray-400 text-left pt-5">--}}
                    {{--                            <span class="material-icons align-middle">--}}
                    {{--                                done--}}
                    {{--                            </span>--}}
                    {{--                        <span class="pl-2">--}}
                    {{--                                Growth <span class="text-black">oriented</span>--}}
                    {{--                            </span>--}}
                    {{--                    </p>--}}
                    {{--                    <p class="font-semibold text-gray-400 text-left pt-5">--}}
                    {{--                            <span class="material-icons align-middle">--}}
                    {{--                                done--}}
                    {{--                            </span>--}}
                    {{--                        <span class="pl-2">--}}
                    {{--                                <span class="text-black">Unlimited</span> cloud storage--}}
                    {{--                            </span>--}}
                    {{--                    </p>--}}

                    <a href="/payments?plan=premium" class=""><p class="w-full lg:py-4 py-2 bg-blue-600 mt-8 rounded-xl text-white font-medium">
                            {{ __("Choose Plan") }}
                        </p></a>
                    {{--                </div>--}}
                </div>
            </div>

        </div>

        <div class="lg:p-6 mt-10 text-center text-white md:px-12 w-11/12 bg-[hsla(0,0%,0%,0.70)] rounded-3xl">

            <h1 class="mt-2 mb-2 text-3xl font-bold tracking-tight md:text-2xl xl:text-3xl">
                {{ __("History") }}
            </h1>


            <div class="lg:py-10 flex my-2 items-center justify-center">
                <div class="overflow-x-auto w-11/12">
                    <table class="bg-white shadow-md rounded-xl w-full">
                        <thead>
                        <tr class="bg-blue-gray-100 text-gray-700">
                            <th class="hidden lg:block py-3 px-4 text-center">מזהה</th>
                            <th class="py-3 px-4 text-center">סיבה</th>
                            <th class="py-3 px-4 text-center">כמות</th>
                            <th class="py-3 px-4 text-center">תאריך</th>
                            <th class="hidden lg:block py-3 px-4 text-center">קישור</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-900">
                        @php /** @var \App\Models\Credit $credit */ @endphp
                        @php /** @var \Illuminate\Database\Eloquent\Collection<array-key, \App\Models\Credit> $credits */ @endphp
                        @foreach($credits->reverse() as $credit)
                        <tr class="border-b border-gray-200">
                            <td class="hidden lg:block py-3 px-4">{{ $credit->id }}</td>
                            <td class="py-3 px-4">{{ $credit->message }}</td>
                            <td class="py-3 px-4" style="direction: ltr">{{ $credit->amount }}</td>
                            <td class="py-3 px-4">{{ $credit->created_at->format("d/m/Y") }}<span class="hidden lg:block">{{ $credit->created_at->format("H:i:s") }}</span></td>
                            <td class="hidden lg:block py-3 px-4">{{ $credit->entity_type }} {{ $credit->entity_id }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex lg:flex-row">
                <div class="flex flex-row w-full">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
