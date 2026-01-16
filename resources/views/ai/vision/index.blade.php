<x-app-layout>
    <x-slot name="title">Image Understanding</x-slot>

    <div class="h-full overflow-y-auto p-6 lg:p-12">
        <div class="max-w-4xl mx-auto space-y-8">
            <div class="space-y-2 text-center lg:text-left">
                <h1 class="text-3xl font-bold tracking-tighter text-white">Image Understanding</h1>
                <p class="text-gray-400">Upload an image and let AI explain what it sees.</p>
            </div>

            <div class="bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-3xl p-8 shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-tr from-purple-500/10 to-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative space-y-6">
                    
                    <!-- File Upload -->
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-300 uppercase tracking-wider">Upload Image</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-600 border-dashed rounded-xl cursor-pointer bg-gray-900/50 hover:bg-gray-800 hover:border-blue-500 transition-all group/upload">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-4 text-gray-400 group-hover/upload:text-blue-500 transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-400"><span class="font-semibold text-blue-400">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 10MB)</p>
                                </div>
                                <input id="dropzone-file" type="file" class="hidden" accept="image/*" onchange="previewImage(event)" />
                            </label>
                        </div>
                        <div id="imagePreviewContainer" class="hidden mt-4 relative w-full h-64 rounded-xl overflow-hidden border border-gray-700">
                             <img id="imagePreview" src="" alt="Preview" class="w-full h-full object-contain bg-black/50" />
                             <button onclick="removeImage()" class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                             </button>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <button 
                        id="analyzeBtn"
                        onclick="analyzeImage()"
                        class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white font-bold py-4 rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg flex items-center justify-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Analyze Image
                    </button>

                    <!-- Loading State -->
                    <div id="loadingState" class="hidden">
                        <div class="flex flex-col items-center justify-center space-y-4 py-8">
                             <div class="relative w-16 h-16">
                                <div class="absolute top-0 left-0 w-full h-full border-4 border-blue-500/30 rounded-full animate-ping"></div>
                                <div class="absolute top-0 left-0 w-full h-full border-4 border-t-blue-500 border-r-transparent border-b-transparent border-l-transparent rounded-full animate-spin"></div>
                             </div>
                             <p class="text-blue-400 font-medium animate-pulse">Analyzing image contents...</p>
                        </div>
                    </div>

                    <!-- Output Area -->
                    <div id="outputContainer" class="hidden space-y-2">
                        <label class="block text-sm font-medium text-gray-300 uppercase tracking-wider">Analysis Result</label>
                        <div class="bg-gray-900 rounded-xl p-6 border border-gray-700 text-gray-100 leading-relaxed font-light min-h-[100px]" id="resultText">
                            <!-- Result will be injected here -->
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="errorContainer" class="hidden p-4 bg-red-900/50 border border-red-500/50 rounded-xl flex items-center gap-3 text-red-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <span id="errorMessage">Something went wrong.</span>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        const imageInput = document.getElementById('dropzone-file');
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const analyzeBtn = document.getElementById('analyzeBtn');
        const outputContainer = document.getElementById('outputContainer');
        const resultText = document.getElementById('resultText');
        const loadingState = document.getElementById('loadingState');
        const errorContainer = document.getElementById('errorContainer');
        const errorMessage = document.getElementById('errorMessage');

        let selectedFile = null;

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                selectedFile = file;
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.classList.remove('hidden');
                    document.querySelector('.group\\/upload').classList.add('hidden');
                }
                reader.readAsDataURL(file);
                
                // Hide errors when new file selected
                errorContainer.classList.add('hidden');
                outputContainer.classList.add('hidden');
            }
        }

        function removeImage() {
            selectedFile = null;
            imageInput.value = '';
            imagePreview.src = '';
            imagePreviewContainer.classList.add('hidden');
            document.querySelector('.group\\/upload').classList.remove('hidden');
            outputContainer.classList.add('hidden');
            errorContainer.classList.add('hidden');
        }

        async function analyzeImage() {
            if (!selectedFile) {
                errorMessage.textContent = 'Please upload an image first.';
                errorContainer.classList.remove('hidden');
                return;
            }

            // Reset UI
            errorContainer.classList.add('hidden');
            outputContainer.classList.add('hidden');
            analyzeBtn.disabled = true;
            analyzeBtn.classList.add('opacity-50', 'cursor-not-allowed');
            loadingState.classList.remove('hidden');
            
            const formData = new FormData();
            formData.append('image', selectedFile);

            try {
                const res = await fetch('{{ route('ai.vision.analyze') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.error || 'Failed to analyze image');
                }

                // Success
                resultText.textContent = data.description;
                outputContainer.classList.remove('hidden');

            } catch (e) {
                console.error(e);
                errorMessage.textContent = e.message;
                errorContainer.classList.remove('hidden');
            } finally {
                loadingState.classList.add('hidden');
                analyzeBtn.disabled = false;
                analyzeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    </script>
</x-app-layout>
