<x-app-layout>
    <div class="p-6 mt-10 text-center dark:text-white text-gray-900 md:px-12 w-3/4 2bg-[hsla(0,0%,0%,0.70)] rounded-3xl min-h-80">
        <h1 class="self-center bg-gradient-to-br from-purple-400 to-pink-500 dark:to-[rgba(122,90,248,1)] bg-clip-text text-3xl md:text-5xl font-semibold text-transparent mb-4">
            {{ __("Craft Tales with Your Little Storyteller") }}
        </h1>

        {{ __("Dive into a world of imagination where you and your child collaborate together to create tales that are uniquely yours, complete with beautiful artwork.") }}

        <x-create-book></x-create-book>

        <div class="flex lg:flex-row">
            <div class="w-full mt-2 lg:mt-4">
                * פרטיותכם חשובה לנו מאוד. כלל הסיפורים והתמונות נשמרים על שרתינו באופן מאובטח ואינם גלויים לשאר המשתמשים.<br>
                * ניתן לשתף סיפורים עם חברים על ידי שליחת קישור האתר.
            </div>
        </div>
    </div>
</x-app-layout>
