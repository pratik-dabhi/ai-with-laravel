<x-app-layout>
    <x-slot name="title">Text to Speech</x-slot>
    <x-slot name="head">
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #1f2937;
                border-radius: 8px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #4b5563;
                border-radius: 8px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #6b7280; 
            }
        </style>
    </x-slot>

    <div class="h-full overflow-y-auto p-6 lg:p-12">
        <div class="max-w-4xl mx-auto space-y-8">
            <div class="space-y-2">
                <h1 class="text-3xl font-bold text-white">Text to Speech</h1>
                <p class="text-gray-400">Convert your text into lifelike speech using AI.</p>
            </div>

            <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                <div class="p-6 space-y-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-300 uppercase tracking-wider">Voice</label>
                        <div class="relative" id="customVoiceDropdown">
                            <!-- Hidden Input -->
                            <input type="hidden" id="voiceId" value="{{ isset($voices[0]['id']) ? $voices[0]['id'] : 'oliver' }}">
                            
                            <!-- Trigger Button -->
                            <button 
                                type="button" 
                                id="voiceDropdownBtn"
                                onclick="toggleVoiceDropdown()"
                                class="w-full bg-gray-900 border border-gray-700 text-gray-100 rounded-xl px-4 py-4 pr-10 text-left outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all flex items-center justify-between group"
                            >
                                <span id="selectedVoiceLabel" class="truncate">
                                    @if(isset($voices) && is_array($voices) && count($voices) > 0)
                                        {{ $voices[0]['display_name'] ?? $voices[0]['name'] ?? $voices[0]['id'] }} ({{ ucfirst($voices[0]['gender'] ?? 'Unknown') }} - {{ $voices[0]['locale'] ?? 'Unknown' }})
                                    @else
                                        Oliver (Default)
                                    @endif
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <!-- Options List -->
                            <div 
                                id="voiceOptionsList" 
                                class="hidden absolute z-50 mt-2 w-full bg-gray-800 border border-gray-700 rounded-xl shadow-2xl max-h-60 overflow-y-auto custom-scrollbar"
                            >
                                <div class="p-2 space-y-1">
                                    @if(isset($voices) && is_array($voices))
                                        @foreach($voices as $voice)
                                            <button 
                                                type="button"
                                                onclick="selectVoice('{{ $voice['id'] }}', '{{ $voice['display_name'] ?? $voice['name'] ?? $voice['id'] }}', '{{ $voice['gender'] ?? 'Unknown' }}', '{{ $voice['locale'] ?? 'Unknown' }}')"
                                                class="w-full flex items-center justify-between px-4 py-3 text-left text-gray-300 hover:bg-gray-700/50 hover:text-white rounded-lg transition-all group"
                                            >
                                                <div class="flex flex-col">
                                                    <span class="font-medium">{{ $voice['display_name'] ?? $voice['name'] ?? $voice['id'] }}</span>
                                                    <span class="text-xs text-gray-500 group-hover:text-gray-400">{{ ucfirst($voice['gender'] ?? 'Unknown') }} • {{ $voice['locale'] ?? 'Unknown' }}</span>
                                                </div>
                                                
                                                @if(isset($voice['preview_audio']))
                                                <div 
                                                    onclick="event.stopPropagation(); playPreview('{{ $voice['preview_audio'] }}', this)"
                                                    class="p-2 rounded-full hover:bg-gray-600 text-gray-500 hover:text-blue-400 transition-colors"
                                                    title="Play Preview"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />
                                                    </svg>
                                                </div>
                                                @endif
                                            </button>
                                        @endforeach
                                    @else
                                        <button 
                                            type="button"
                                            onclick="selectVoice('oliver', 'Oliver', 'Male', 'en-US')"
                                            class="w-full text-left px-4 py-3 text-gray-300 hover:bg-gray-700/50 hover:text-white rounded-lg transition-all"
                                        >
                                            Oliver (Default)
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <label class="block text-sm font-medium text-gray-300 uppercase tracking-wider">Input Text</label>
                    <textarea 
                        id="prompt" 
                        rows="6" 
                        class="w-full bg-gray-900 border border-gray-700 text-gray-100 rounded-xl p-4 outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-gray-500" 
                        placeholder="Enter the text you want to convert to speech..."
                    ></textarea>

                    <div class="flex flex-col md:flex-row gap-4 pt-2">
                        <button onclick="generateSpeech()" id="generateBtn" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-medium py-3 px-6 rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                            </svg>
                            Generate Speech
                        </button>
                    </div>
                </div>
            </div>

            {{-- OUTPUT --}}
            <div id="outputContainer" class="hidden bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden shadow-xl">
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-300 uppercase tracking-wider">Output Audio</label>
                    </div>

                    <div class="flex items-center justify-center py-4">
                        <audio id="audioPlayer" controls class="w-full">
                            <source id="audioSource" src="" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                    
                    <div id="messageContainer" class="text-sm text-green-400 font-medium hidden"></div>
                </div>
            </div>
            
            {{-- ERROR --}}
             <div id="errorContainer" class="hidden bg-red-900/20 rounded-2xl border border-red-700/50 overflow-hidden shadow-xl">
                <div class="p-6">
                     <p id="errorMessage" class="text-red-400 font-medium"></p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        const promptInput = document.getElementById('prompt');
        const voiceInput = document.getElementById('voiceId');
        const voiceDropdownBtn = document.getElementById('voiceDropdownBtn');
        const voiceOptionsList = document.getElementById('voiceOptionsList');
        const selectedVoiceLabel = document.getElementById('selectedVoiceLabel');
        
        const generateBtn = document.getElementById('generateBtn');
        const outputContainer = document.getElementById('outputContainer');
        const audioPlayer = document.getElementById('audioPlayer');
        const audioSource = document.getElementById('audioSource');
        const errorContainer = document.getElementById('errorContainer');
        const errorMessage = document.getElementById('errorMessage');
        const messageContainer = document.getElementById('messageContainer');

        let currentPreviewAudio = null;

        /* ---------- DROPDOWN LOGIC ---------- */
        function toggleVoiceDropdown() {
            voiceOptionsList.classList.toggle('hidden');
        }

        function selectVoice(id, name, gender, locale) {
            voiceInput.value = id;
            selectedVoiceLabel.textContent = `${name} (${gender} - ${locale})`;
            voiceOptionsList.classList.add('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!voiceDropdownBtn.contains(event.target) && !voiceOptionsList.contains(event.target)) {
                voiceOptionsList.classList.add('hidden');
            }
        });

        /* ---------- PREVIEW LOGIC ---------- */
        function playPreview(url, btn) {
            if (currentPreviewAudio) {
                currentPreviewAudio.pause();
                currentPreviewAudio = null;
            }

            const audio = new Audio(url);
            currentPreviewAudio = audio;
            audio.play();
            
            // Optional: Visual feedback could be added here
            audio.onended = () => {
                currentPreviewAudio = null;
            };
        }

        /* ---------- GENERATE LOGIC ---------- */
        async function generateSpeech() {
            if(!promptInput.value.trim()) return;
            
            // Reset state
            outputContainer.classList.add('hidden');
            errorContainer.classList.add('hidden');
            messageContainer.classList.add('hidden');
            generateBtn.disabled = true;
            generateBtn.classList.add('opacity-50', 'cursor-not-allowed');
            generateBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Generating...
            `;
            
            try {
                const res = await fetch('{{ route('ai.text-to-speech.generate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        text: promptInput.value,
                        voice_id: voiceInput.value
                    })
                });

                const data = await res.json();

                if (!res.ok) {
                   throw new Error(data.error || 'Something went wrong');
                }
                
                // Success
                audioSource.src = data.audio_url;
                audioPlayer.load();
                outputContainer.classList.remove('hidden');
                
                messageContainer.textContent = data.message;
                messageContainer.classList.remove('hidden');

            } catch(e) {
                console.error(e);
                errorMessage.textContent = 'Error: ' + e.message;
                errorContainer.classList.remove('hidden');
            } finally {
                generateBtn.disabled = false;
                generateBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                generateBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                       <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                    </svg>
                    Generate Speech
                `;
            }
        }
    </script>
    @endpush
</x-app-layout>
