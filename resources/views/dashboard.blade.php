<x-app-layout>
    <div class="p-6 mt-10 text-center dark:text-white text-gray-900 md:px-12 w-3/4 2bg-[hsla(0,0%,0%,0.70)] rounded-3xl min-h-80">
        <h1 class="self-center bg-gradient-to-br from-purple-400 to-pink-500 dark:to-[rgba(122,90,248,1)] bg-clip-text text-3xl md:text-5xl font-semibold text-transparent mb-4">
            {{ __("Craft Tales with Your Little Storyteller") }}
        </h1>

        <form id="create-book" class="flex flex-col">
            {{ __("Dive into a world of imagination where you and your child collaborate together to create tales that are uniquely yours, complete with beautiful artwork.") }}
            @csrf
            <div class="mt-3 flex flex-col lg:flex-row lg:justify-center lg:gap-3 gap-y-1">
                <select name="age" class="bg-[hsla(0,0%,100%,0.50)] dark:bg-[hsla(0,0%,100%,0.05)] dark:border-2 rounded dark:border-white focus:border-white w-full lg:w-1/6">
                    <option class="dark:bg-black" disabled selected>-בחר גיל-</option>
                    <option class="dark:bg-black">3-5</option>
                    <option class="dark:bg-black">6-8</option>
                    <option class="dark:bg-black">9-12</option>
                    <option class="dark:bg-black" disabled>12+</option>
                </select>

{{--                <input type="text" name="subject1" class="bg-black border-2 rounded border-white focus:border-white lg:w-1/5">--}}
{{--                <input type="text" name="subject2" class="bg-black border-2 rounded border-white focus:border-white lg:w-1/5">--}}
                <select name="moral" class="bg-[hsla(0,0%,100%,0.50)] dark:bg-[hsla(0,0%,100%,0.05)] dark:border-2 rounded dark:border-white focus:border-white w-full lg:w-1/5">
                    <option class="dark:bg-black" disabled selected>-בחר ערך חינוכי-</option>
                    <option class="dark:bg-black" value="parent-respact">כיבוד הורים</option>
                    <option class="dark:bg-black" value="friendship">חברות</option>
                    <option class="dark:bg-black" value="helping-others">עזרה לזולת</option>
                    <option class="dark:bg-black" value="random">אקראי</option>
                    <option class="dark:bg-black" value="none">ללא</option>
                </select>

                <div class="w-full lg:w-1/6 text-right lg:text-center flex items-center justify-center">
                    <label>
                        <input type="checkbox" name="isAdultReader" class="bg-[hsla(0,0%,100%,0.05)] dark:bg-[hsla(0,0%,100%,0.10)] dark:border-2 rounded dark:border-white focus:border-white">
                        המקריא בגיר
                    </label>
                </div>

                <select name="language" class="bg-[hsla(0,0%,100%,0.50)] dark:bg-[hsla(0,0%,100%,0.05)] dark:border-2 rounded dark:border-white focus:border-white w-full lg:w-1/6">
                    <option class="dark:bg-black" disabled>-בחר שפה-</option>
                    <option class="dark:bg-black" value="he" selected>עברית</option>
                    <option class="dark:bg-black" value="en">אנגלית</option>
                </select>

                <select name="art-style" class="bg-[hsla(0,0%,100%,0.50)] dark:bg-[hsla(0,0%,100%,0.05)] dark:border-2 rounded dark:border-white focus:border-white w-full lg:w-1/6">
                    <option class="dark:bg-black" disabled selected>-עיצוב-</option>
                    <option class="dark:bg-black" value="Pixar">Pixar</option>
                    <option class="dark:bg-black" value="Walt Disney">וולט דיסני</option>
                    <option class="dark:bg-black" value="Anime">אנימה</option>
                    <option class="dark:bg-black" value="asked-in-text">אציין בעצמי</option>
                    <option class="dark:bg-black" value="random">אקראי</option>
                </select>

{{--                <div class="w-full lg:w-1/6 text-right lg:text-center">--}}
{{--                    <label>--}}
{{--                        <input type="checkbox" checked name="pictures" class="bg-black border-2 rounded border-white focus:border-white">--}}
{{--                        צור תמונות--}}
{{--                    </label>--}}
{{--                </div>--}}
            </div>
            <textarea required minlength="15" id="plot" name="plot" cols="4" maxlength="500" class="mt-4 h-28 bg-[hsla(0,0%,100%,0.50)] dark:bg-[hsla(0,0%,100%,0.10)] dark:border-2 rounded dark:border-white  font-medium dark:text-white focus:border-white"></textarea>
            <x-button class="mt-4 text-center flex items-center justify-center" id="submit" type="submit">
                {{ __("Begin Your Adventure") }}
            </x-button>

            <div id="spin" class="mt-2" style="display: none">
                <div class="mt-4" id="book-spinner">
                    <svg aria-hidden="true" class=" mx-auto inline w-8 h-8 text-white animate-spin dark:text-gray-800 fill-purple-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                    </svg>
                </div>

                <div id="book-status-message" class="mt-4"></div>
            </div>
        </form>
    </div>

    <script>
        const words = ["חבורת ילדים בני 8 ממושב בשפלה בונים מחנה בנחל שליד הבית", "ספר ילדים עם איורים בסגנון וולט דיסני שמספר על הרפתאותיו של יונתן בספארי בדרום אפריקה", "קים אוהבת ללכת לחוג בלט אך לאחרונה פחות מתחשק לה. נא ליצור ספר שיעודד אותה ללכת לחוג וילמד אותה על התמדה"];
        const plotElement = document.getElementById("plot");
        let iTyping = 0;
        let jTyping = 0;
        let currentWord = "";
        let isDeleting = false;

        function type() {
            currentWord = words[iTyping];
            if (isDeleting) {
                plotElement.placeholder = currentWord.substring(0, jTyping-1);
                jTyping--;
                if (jTyping === 0) {
                    isDeleting = false;
                    iTyping++;
                    if (iTyping === words.length) {
                        iTyping = 0;
                    }
                }
            } else {
                plotElement.placeholder = currentWord.substring(0, jTyping+1);
                jTyping++;
                if (jTyping === currentWord.length) {
                    isDeleting = true;

                    setTimeout(type, 3000);
                    return
                }
            }
            setTimeout(type, 100);
        }

        type();


        const spinnerElement = document.getElementById("book-spinner");
        const statusMessageElement = document.getElementById("book-status-message");

        document.getElementById('create-book').addEventListener('submit', function(e) {
            // Prevent the default form submit behavior
            e.preventDefault();
            document.getElementById('spin').style.display = '';
            document.getElementById('submit').disabled = true;
            spinnerElement.classList.remove("hidden");
            statusMessageElement.textContent = '';

            // Create an object to populate with the form data
            const data = {};
            // Collect form data
            (new FormData(this)).forEach((value, key) => (data[key] = value));

            // Use fetch to send the data as a POST request
            fetch('/books', {
                method: 'POST', // Specify the request method
                headers: {
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
                    query(data.uuid);
                })
                .catch((error) => {
                    console.error('Error:', error); // Handle errors
                    statusMessageElement.textContent = error.message;
                    document.getElementById('submit').disabled = false;
                    spinnerElement.classList.add("hidden");
                });
        });

        function query(uuid) {
            fetch("/books/"+uuid, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json(); // This converts the response body to JSON
                })
                .then(book => {
                    console.log(book);

                    statusMessageElement.textContent = book.status_message;
                    if ({{\App\Enums\BookStatuses::Ready->value}} === book.status) {
                        setTimeout(() => { window.location.href = "/books/" + book.uuid; }, 3000);
                        return;
                    }

                    if (![{{\App\Enums\BookStatuses::FailedText->value}}, {{\App\Enums\BookStatuses::FailedImages->value}}, {{\App\Enums\BookStatuses::Ready->value}}].includes(book.status)) {
                        setTimeout(() => { query(uuid); }, 3000);
                        return;
                    }

                    spinnerElement.classList.add("hidden");
                })
                .catch(e => {
                    console.error('There has been a problem with your fetch operation:', e);
                });
        }
    </script>

</x-app-layout>
