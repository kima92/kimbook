<x-app-layout>
    <div class="p-6 mt-10 text-center text-white md:px-12 w-3/4 bg-[hsla(0,0%,0%,0.70)] rounded-3xl min-h-80">
        <h1 class="mt-2 mb-16 text-3xl font-bold tracking-tight md:text-2xl xl:text-3xl">
            {{ __("Craft Tales with Your Little Storyteller") }}
        </h1>

        <form hx-post="/books" hx-target="#spin" hx-swap="outerHTML" class="flex flex-col">
            {{ __("Dive into a world of imagination where you and your child collaborate together to create tales that are uniquely yours, complete with beautiful artwork.") }}
            @csrf
            <div class="mt-3 flex flex-col lg:flex-row lg:justify-center lg:gap-3 gap-y-1">
                <select name="age" class="bg-black border-2 rounded border-white focus:border-white w-full lg:w-1/6">
                    <option disabled selected>-בחר גיל-</option>
                    <option>3-5</option>
                    <option>6-8</option>
                    <option>9-12</option>
                    <option disabled>12+</option>
                </select>

{{--                <input type="text" name="subject1" class="bg-black border-2 rounded border-white focus:border-white lg:w-1/5">--}}
{{--                <input type="text" name="subject2" class="bg-black border-2 rounded border-white focus:border-white lg:w-1/5">--}}
                <select name="moral" class="bg-black border-2 rounded border-white focus:border-white w-full lg:w-1/5">
                    <option disabled selected>-בחר ערך חינוכי-</option>
                    <option value="parent-respact">כיבוד הורים</option>
                    <option value="friendship">חברות</option>
                    <option value="helping-others">עזרה לזולת</option>
                    <option value="random">אקראי</option>
                    <option value="none">ללא</option>
                </select>

                <div class="w-full lg:w-1/6 text-right lg:text-center">
                    <label>
                        <input type="checkbox" name="isAdultReader" class="bg-black border-2 rounded border-white focus:border-white">
                        המקריא בגיר
                    </label>
                </div>

                <select name="language" class="bg-black border-2 rounded border-white focus:border-white w-full lg:w-1/6">
                    <option disabled>-בחר שפה-</option>
                    <option value="he" selected>עברית</option>
                    <option value="en">אנגלית</option>
                </select>

                <div class="w-full lg:w-1/6 text-right lg:text-center">
                    <label>
                        <input type="checkbox" checked name="pictures" class="bg-black border-2 rounded border-white focus:border-white">
                        צור תמונות
                    </label>
                </div>
            </div>
            <textarea required minlength="15" id="plot" name="plot" cols="4" maxlength="500" class="mt-4 h-28 rounded border-2 bg-[hsla(0,0%,0%,0.70)] border-white font-medium text-white focus:border-white"></textarea>
            <button type="submit" class="mt-4 rounded border-2 border-white px-[46px] pt-[14px] pb-[12px] text-sm font-medium uppercase leading-normal text-white transition duration-150 ease-in-out hover:border-green-200 hover:bg-green-200 hover:bg-opacity-10 hover:text-green-200 focus:border-green-200 focus:text-green-200 focus:outline-none focus:ring-0 active:border-green-200 active:text-green-200">
                {{ __("Begin Your Adventure") }}
            </button>

            <div id="spin" class="mt-2" style="display: none">

            </div>
        </form>
    </div>

    <script>
        const words = ["חבורת ילדים בני 8 ממושב בשפלה בונים מחנה בנחל שליד הבית", "ספר ילדים עם איורים בסגנון וולט דיסני שמספר על הרפתאותיו של יונתן בספארי בדרום אפריקה", "קים אוהבת ללכת לחוג בלט אך לאחרונה פחות מתחשק לה. נא ליצור ספר שיעודד אותה ללכת לחוג וילמד אותה על התמדה"];
        const plotElement = document.getElementById("plot");
        let i = 0;
        let j = 0;
        let currentWord = "";
        let isDeleting = false;

        function type() {
            currentWord = words[i];
            if (isDeleting) {
                plotElement.placeholder = currentWord.substring(0, j-1);
                j--;
                if (j === 0) {
                    isDeleting = false;
                    i++;
                    if (i === words.length) {
                        i = 0;
                    }
                }
            } else {
                plotElement.placeholder = currentWord.substring(0, j+1);
                j++;
                if (j === currentWord.length) {
                    isDeleting = true;

                    setTimeout(type, 3000);
                    return
                }
            }
            setTimeout(type, 100);
        }

        type();
    </script>

</x-app-layout>
