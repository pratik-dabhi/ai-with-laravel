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
                    </div>
                </div>
            </div>

            {{-- OUTPUT --}}
            <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-300 uppercase tracking-wider">Output</label>
                        <button onclick="document.getElementById('output').value = ''" class="text-xs text-gray-500 hover:text-white transition-colors">Clear</button>
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

        /* ---------- TEXT ---------- */
        async function generateText() {
            if(!promptInput.value.trim()) return;
            
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
            
            output.value = '';
            
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
    </script>
    @endpush
</x-app-layout>
