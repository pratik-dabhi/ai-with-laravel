<x-app-layout>
    <x-slot name="title">AI Playground</x-slot>

    <div class="h-full overflow-y-auto p-6 lg:p-12">
        <div class="max-w-4xl mx-auto space-y-8">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-white">Playground</h1>
                <p class="text-gray-400">Test different prompts and models directly with Prism and Ollama.</p>
            </div>

            <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                <div class="p-6 space-y-4">
                    <label class="block text-sm font-medium text-gray-300 uppercase tracking-wider">Prompt</label>
                    <textarea 
                        id="prompt" 
                        rows="4" 
                        class="w-full bg-gray-900 border border-gray-700 text-gray-100 rounded-xl p-4 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-500" 
                        placeholder="Enter your system prompt or user query here..."
                    ></textarea>

                    <div class="flex flex-col md:flex-row gap-4 pt-2">
                        <button onclick="generateText()" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                            </svg>
                            Generate Text
                        </button>

                        <button onclick="generateStream()" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-medium py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            Stream Response
                        </button>

                        <button onclick="generateImage()" id="imageBtn" class="flex-1 bg-purple-600 hover:bg-purple-500 text-white font-medium py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Generate Image
                        </button>
                    </div>
                </div>
            </div>

            {{-- OUTPUT --}}
            <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-300 uppercase tracking-wider">Output</label>
                        <button onclick="clearOutput()" class="text-xs text-gray-500 hover:text-white transition-colors">Clear</button>
                    </div>
                    
                    <div id="imageOutput" class="hidden space-y-4">
                        <div class="relative group aspect-square max-w-lg mx-auto overflow-hidden rounded-xl border border-gray-700 bg-gray-900">
                            <img id="generatedImage" src="" alt="Generated image" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <a id="downloadLink" href="" target="_blank" class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
                                    Open Original
                                </a>
                            </div>
                        </div>
                    </div>

                    <textarea 
                        id="output" 
                        rows="12" 
                        class="w-full bg-gray-900 border border-gray-700 text-gray-100 rounded-xl p-4 outline-none font-mono text-sm leading-relaxed" 
                        readonly 
                        placeholder="AI response will appear here..."
                    ></textarea>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const promptInput = document.getElementById('prompt');
        const output = document.getElementById('output');
        const imageOutput = document.getElementById('imageOutput');
        const generatedImage = document.getElementById('generatedImage');
        const downloadLink = document.getElementById('downloadLink');
        const imageBtn = document.getElementById('imageBtn');

        function clearOutput() {
            output.value = '';
            imageOutput.classList.add('hidden');
            generatedImage.src = '';
            output.classList.remove('hidden');
        }

        /* ---------- TEXT ---------- */
        async function generateText() {
            if(!promptInput.value.trim()) return;
            
            clearOutput();
            output.value = 'Generating...';
            
            try {
                const res = await fetch('/ai/text', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        prompt: promptInput.value
                    })
                });

                const data = await res.json();
                output.value = data.content ?? 'No response';
            } catch(e) {
                output.value = 'Error: ' + e.message;
            }
        }

        /* ---------- STREAM ---------- */
        function generateStream() {
            if(!promptInput.value.trim()) return;
            
            clearOutput();
            
            const url = `/ai/stream?prompt=${encodeURIComponent(promptInput.value)}`;
            const evtSource = new EventSource(url);

            evtSource.onmessage = (event) => {
                output.value += event.data;
                output.scrollTop = output.scrollHeight;
            };
            
            evtSource.addEventListener('text_delta', (event) => {
                const payload = JSON.parse(event.data);
                output.value += payload.delta;
                output.scrollTop = output.scrollHeight;
            });

            evtSource.addEventListener('stream_end', () => {
                evtSource.close();
            });

            evtSource.onerror = () => {
                evtSource.close();
            };
        }

        /* ---------- IMAGE ---------- */
        async function generateImage() {
            if(!promptInput.value.trim()) return;

            clearOutput();
            output.value = 'Initiating image generation...';
            imageBtn.disabled = true;
            imageBtn.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const res = await fetch('/ai/image', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        prompt: promptInput.value
                    })
                });

                const data = await res.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                if (data.status === 'processing') {
                    output.value = 'Image request accepted. Processing (Task ID: ' + data.task_id + ')...\nThis may take up to a minute.';
                    pollImageStatus(data.task_id);
                }
            } catch (e) {
                output.value = 'Error: ' + e.message;
                imageBtn.disabled = false;
                imageBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        async function pollImageStatus(taskId) {
            const interval = setInterval(async () => {
                try {
                    const res = await fetch(`/ai/image/status/${taskId}`);
                    const data = await res.json();

                    if (data.status === 'completed') {
                        clearInterval(interval);
                        output.value = 'Image generation completed!';
                        output.classList.add('hidden');
                        
                        generatedImage.src = data.url;
                        downloadLink.href = data.url;
                        imageOutput.classList.remove('hidden');
                        
                        imageBtn.disabled = false;
                        imageBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else if (data.status === 'failed') {
                        clearInterval(interval);
                        output.value = 'Error: ' + (data.error || 'Generation failed');
                        imageBtn.disabled = false;
                        imageBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        output.value += '.';
                    }
                } catch (e) {
                    clearInterval(interval);
                    output.value = 'Error polling status: ' + e.message;
                    imageBtn.disabled = false;
                    imageBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }, 3000);
        }
    </script>
    @endpush
</x-app-layout>
