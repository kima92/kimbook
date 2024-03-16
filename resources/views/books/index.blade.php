<x-app-layout>
    <div class="flex flex-col">
        @if(Auth::user()->books->isEmpty())
        <div class="p-6 mt-10 text-center text-white md:px-12 bg-[hsla(0,0%,0%,0.70)] rounded-3xl">
            <h1 class="mt-2 mb-6 text-3xl font-bold tracking-tight md:text-2xl xl:text-3xl">
                {{ __("No books created yet") }}
            </h1>
            <form method="get" action="/dashboard">
                <button id="submit" type="submit" class="rounded border-2 border-white px-[46px] pt-[14px] pb-[12px] text-sm font-medium uppercase leading-normal text-white transition duration-150 ease-in-out hover:border-green-200 hover:bg-green-200 hover:bg-opacity-10 hover:text-green-200 focus:border-green-200 focus:text-green-200 focus:outline-none focus:ring-0 active:border-green-200 active:text-green-200">
                    צור הרפתקה חדשה
                </button>
            </form>
        </div>
        @endif
        <x-books-grid :books="Auth::user()->books"/>
    </div>
</x-app-layout>
