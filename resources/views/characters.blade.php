<x-app-layout>
    <div class="flex flex-col justify-center items-center w-11/12 ">
        <div class="lg:p-6 mt-10 text-center dark:text-white text-black md:px-12 w-11/12 rounded-3xl">

            <h1 class="mt-2 text-3xl font-bold text-purple-900 dark:text-white tracking-tight md:text-2xl xl:text-3xl">
                {{ __("My Characters") }}
            </h1>

            <div class="lg:py-5 flex my-1 items-center justify-center">
                <div class="grid lg:grid-cols-3 lg:gap-y-8 gap-y-2 gap-x-16 lg:mt-10 w-11/12 justify-center mx-auto">
                    @php /** @var \App\Models\Character $character */ @endphp
                    @foreach($characters as $character)
                    <div class="w-80">
                        <div class=" w-80 h-80 bg-pink-200 flex justify-center rounded-full p-5
                        before:absolute before:inset-0 before:bg-[linear-gradient(315deg,#03a9f4,#8055a6,#c0005e)] before:rounded-[2rem]
                        after:absolute   after:inset-0  after:bg-[linear-gradient(315deg,#03a9f4,#8055a6,#c0005e)]  after:blur-[30px]
                        text-black  transform transition duration-500 hover:scale-110">
                            <b class="z-10 rounded-[2rem] absolute inset-1.5 overflow-hidden bg-cover bg-no-repeat lg:p-30 h-max"
                               style="background-image: url('{{ $character->image_path }}');height: 307px;width: 307px"></b>
                            <div class="z-20">
                                <h3 class="text-2xl text-center bg-[hsla(0,0%,100%,0.70)] leading-6 mx-auto content z-20 p-2 rounded-3xl" style="direction: rtl;">
                                    {{ $character->name }}
                                </h3>
                            </div>
                        </div>

                        <h3 class="text-lg text-center leading-6 mx-auto z-20 p-2 rounded-3xl mt-2" style="direction: rtl;">
                            {{ $character->description }}
                        </h3>
                    </div>
                    @endforeach
                </div>
            </div>

            <h1 class="mt-2 text-3xl font-bold text-purple-900 dark:text-white tracking-tight md:text-2xl xl:text-3xl">
                {{ __("Create New Character") }}
            </h1>
            <form method="POST" action="{{ route('characters-store') }}" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col lg:flex-row">
                    <div class="flex flex-col items-start lg:w-3/4 w-full">
                        <label for="name">{{ __("Name") }}</label>
                        <input required id="name" name="name" class="mb-4 lg:w-1/4 w-full bg-[hsla(0,0%,100%,0.50)] dark:bg-[hsla(0,0%,100%,0.10)] dark:border-2 rounded dark:border-white  font-medium dark:text-white focus:border-white">
                        <label for="name">{{ __("Description") }}</label>
                        <textarea name="description" cols="4" maxlength="500" class="mb-4 h-28 lg:w-11/12 w-full bg-[hsla(0,0%,100%,0.50)] dark:bg-[hsla(0,0%,100%,0.10)] dark:border-2 rounded dark:border-white  font-medium dark:text-white focus:border-white"></textarea>
                    </div>
                    <label for="dropzone-file" id="dropzone" class="flex flex-row items-center justify-center lg:w-1/4 w-full h-95 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">{{ __('Click to upload') }}</span> {{ __('or drag and drop') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG {{ __('or') }} GIF ({{ __('MAX.') }} 800x400px)</p>
                        </div>
                        <input id="dropzone-file" type="file" name="file" required class="hidden" accept=".jpg, .png" />
                        <div class="flex p-1">
                            <img src="" class="mt-4 mx-auto max-h-40 hidden" id="preview">
                            <iframe id="preview-container" class="flex m-4 mx-auto h-80 hidden" ></iframe>
                        </div>
                    </label>

                </div>
                <x-button class="mb-4 text-center flex items-center justify-center mt-4 lg:mt-2" id="submit" type="submit">
                    {{ __("Create") }}
                </x-button>
            </form>

            <div class="flex lg:flex-row">
                <div class="flex flex-row w-full">
                    * מומלץ לבחור תמונת חצי גוף עליון. תמונה קרובה מידי בה רואים רק פנים תפגע ביצירתיות התוצרים
                </div>
            </div>
        </div>
    </div>

        <script>
            var dropzone = document.getElementById('dropzone');

            dropzone.addEventListener('dragover', e => {
                e.preventDefault();
                dropzone.classList.add('border-indigo-600');
            });

            dropzone.addEventListener('dragleave', e => {
                e.preventDefault();
                dropzone.classList.remove('border-indigo-600');
            });

            dropzone.addEventListener('drop', e => {
                e.preventDefault();
                dropzone.classList.remove('border-indigo-600');
                var file = e.dataTransfer.files[0];
                displayPreview(file);
            });

            var input = document.getElementById('dropzone-file');

            input.addEventListener('change', e => {
                var file = e.target.files[0];
                displayPreview(file);
            });


            var reader = new FileReader();
            var preview = document.getElementById('preview');
            var previewPDF = document.getElementById('preview-container');
            function displayPreview(file) {

                preview.src = null;
                preview.classList.add('hidden');
                previewPDF.src = null;
                previewPDF.classList.add('hidden');

                // Check the file type
                if (file.type.match('image.*')) {
                    // It's an image file, handle it as before
                    reader.readAsDataURL(file);
                    reader.onload = () => {
                        preview.src = reader.result;
                        preview.classList.remove('hidden');
                    };
                } else if (file.type === 'application/pdf') {
                    // It's a PDF file, handle accordingly
                    reader.readAsArrayBuffer(file);
                    reader.onload = () => {
                        previewPDF.src = URL.createObjectURL(new Blob([reader.result], { type: 'application/pdf' }));
                        previewPDF.classList.remove('hidden');
                    };
                } else {
                    // File type not supported
                    console.log('File type not supported');
                }
            }
        </script>


</x-app-layout>
