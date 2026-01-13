<x-app-layout>
    <x-slot name="title">Speech to Text</x-slot>

    <div class="h-full overflow-y-auto p-6 lg:p-12 bg-gray-900">
        <div class="max-w-4xl mx-auto space-y-8">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-white">Speech to Text</h1>
                <p class="text-gray-400">Transform your audio recordings into accurate text transcriptions using advanced AI technology.</p>
            </div>

            <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                <div class="p-6 space-y-6">
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                            <span class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                            Upload Your Audio
                        </h2>
                        <p class="text-sm text-gray-400">Select an audio file (MP3, WAV, M4A, etc.) to transcribe. Max size: 10MB.</p>
                    </div>

                    <form id="transcribeForm" class="space-y-6">
                        @csrf
                        <div class="relative group">
                            <input type="file" name="audio" id="audio" accept="audio/*" class="hidden" required />
                            <label for="audio" id="dropArea" class="flex flex-col items-center justify-center w-full min-h-[200px] border-2 border-dashed border-gray-700 rounded-2xl bg-gray-900/50 hover:bg-gray-900 hover:border-emerald-500/50 transition-all cursor-pointer p-8 group">
                                <div class="bg-gray-800 p-4 rounded-xl mb-4 group-hover:scale-110 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-emerald-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-medium text-white mb-1" id="uploadText">Click to browse or drag and drop</p>
                                    <p class="text-sm text-gray-500">Supports MP3, WAV, M4A, OGG</p>
                                </div>
                            </label>

                            <div id="fileInfo" class="hidden absolute inset-0 bg-gray-800 rounded-2xl flex items-center justify-between px-8 border border-emerald-500/30">
                                <div class="flex items-center gap-4">
                                    <div class="bg-emerald-500/10 p-3 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-emerald-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p id="fileName" class="text-white font-medium truncate max-w-[200px]"></p>
                                        <p id="fileSize" class="text-xs text-gray-500"></p>
                                    </div>
                                </div>
                                <button type="button" id="removeFile" class="text-gray-500 hover:text-white transition-colors p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div id="errorContainer" class="hidden">
                            <div class="bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded-xl text-sm flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                <span id="errorMessage"></span>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" id="submitIcon">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75" />
                            </svg>
                            <span id="submitText">Transcribe Audio</span>
                        </button>
                    </form>
                </div>
            </div>

            <div id="transcriptResult" class="hidden space-y-4 animate-in fade-in slide-in-from-bottom-4 duration-500">
                <div class="bg-gray-800 rounded-2xl border border-emerald-500/30 overflow-hidden shadow-xl">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                                <span class="bg-emerald-500/20 p-1 rounded-md text-emerald-500 text-sm">✓</span>
                                Transcription Result
                            </h2>
                            <button onclick="copyTranscript()" class="text-xs text-emerald-500 hover:text-emerald-400 font-medium transition-colors border border-emerald-500/30 px-3 py-1.5 rounded-lg bg-emerald-500/5">
                                Copy Text
                            </button>
                        </div>
                        
                        <div class="bg-gray-900 rounded-xl p-6 border border-gray-700 min-h-[150px]">
                            <p id="transcriptText" class="text-gray-200 leading-relaxed font-mono text-sm whitespace-pre-wrap"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-500/5 border border-blue-500/20 rounded-2xl p-6">
                <h3 class="text-blue-400 font-semibold mb-3 flex items-center gap-2 uppercase tracking-wider text-xs">
                    Tips for Best Results
                </h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li class="flex items-start gap-3">
                        <span class="text-blue-500 mt-0.5">•</span>
                        Use high-quality audio with minimal background noise.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-500 mt-0.5">•</span>
                        Ensure clear speech with proper pronunciation.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-500 mt-0.5">•</span>
                        Files under 10MB typically process faster.
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const form = document.getElementById('transcribeForm');
        const fileInput = document.getElementById('audio');
        const dropArea = document.getElementById('dropArea');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeFile = document.getElementById('removeFile');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitIcon = document.getElementById('submitIcon');
        const errorContainer = document.getElementById('errorContainer');
        const errorMessage = document.getElementById('errorMessage');
        const transcriptResult = document.getElementById('transcriptResult');
        const transcriptText = document.getElementById('transcriptText');

        // File Selection
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFileSelect(this.files[0]);
            }
        });

        // Drag and Drop
        ['dragenter', 'dragover'].forEach(name => {
            dropArea.addEventListener(name, (e) => {
                e.preventDefault();
                dropArea.classList.add('border-emerald-500', 'bg-emerald-500/5');
            });
        });

        ['dragleave', 'drop'].forEach(name => {
            dropArea.addEventListener(name, (e) => {
                e.preventDefault();
                dropArea.classList.remove('border-emerald-500', 'bg-emerald-500/5');
            });
        });

        dropArea.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        function handleFileSelect(file) {
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
            fileInfo.classList.remove('hidden');
            submitBtn.disabled = false;
            errorContainer.classList.add('hidden');
        }

        removeFile.addEventListener('click', () => {
            fileInput.value = '';
            fileInfo.classList.add('hidden');
            submitBtn.disabled = true;
            transcriptResult.classList.add('hidden');
        });

        // Form Submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('audio', fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            setLoading(true);
            errorContainer.classList.add('hidden');
            transcriptResult.classList.add('hidden');

            try {
                const response = await fetch('{{ route('ai.speech-to-text.transcribe') }}', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Something went wrong');
                }

                transcriptText.textContent = data.transcript;
                transcriptResult.classList.remove('hidden');
                transcriptResult.scrollIntoView({ behavior: 'smooth' });
            } catch (err) {
                errorMessage.textContent = err.message;
                errorContainer.classList.remove('hidden');
            } finally {
                setLoading(false);
            }
        });

        function setLoading(isLoading) {
            submitBtn.disabled = isLoading;
            submitText.textContent = isLoading ? 'Transcribing...' : 'Transcribe Audio';
            if (isLoading) {
                submitIcon.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            } else {
                submitIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75" /></svg>';
            }
        }

        async function copyTranscript() {
            try {
                await navigator.clipboard.writeText(transcriptText.textContent);
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = 'Copied!';
                btn.classList.add('bg-emerald-500/20');
                setTimeout(() => {
                    btn.textContent = originalText;
                    btn.classList.remove('bg-emerald-500/20');
                }, 2000);
            } catch (err) {
                console.error('Failed to copy text: ', err);
            }
        }
    </script>
    @endpush
</x-app-layout>