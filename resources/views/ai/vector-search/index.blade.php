<x-app-layout>
    <x-slot name="title">Vector Search</x-slot>

    <div class="h-full overflow-y-auto p-6 lg:p-12">
        <div class="max-w-6xl mx-auto space-y-8">
            <div class="space-y-2 text-center lg:text-left">
                <h1 class="text-3xl font-bold tracking-tighter text-white">Semantic Search</h1>
                <p class="text-gray-400">Store knowledge and retrieve it using natural language queries (RAG Demo).</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Left: Add Knowledge -->
                <div class="bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-3xl p-8 shadow-2xl space-y-6">
                    <div>
                         <h2 class="text-xl font-semibold text-white mb-2">1. Add Knowledge</h2>
                         <p class="text-sm text-gray-400">Add text to the vector database. It will be embedded and stored.</p>
                    </div>

                    <textarea id="documentContent" rows="6" class="w-full bg-gray-900 border border-gray-700 rounded-xl p-4 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none" placeholder="Enter text to store (e.g. 'Laravel is a web application framework with expressive, elegant syntax...')..."></textarea>

                    <button onclick="storeDocument()" id="storeBtn" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Store Document
                    </button>
                    <p id="storeMessage" class="text-center text-sm font-medium hidden"></p>
                </div>

                <!-- Right: Search Knowledge -->
                <div class="bg-gray-800/50 backdrop-blur-xl border border-gray-700/50 rounded-3xl p-8 shadow-2xl space-y-6 flex flex-col h-[500px]">
                    <div>
                         <h2 class="text-xl font-semibold text-white mb-2">2. Semantic Search</h2>
                         <p class="text-sm text-gray-400">Search your knowledge base using natural language.</p>
                    </div>

                    <div class="relative">
                        <input type="text" id="searchQuery" class="w-full bg-gray-900 border border-gray-700 rounded-xl p-4 pl-12 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" placeholder="Ask a question..." onkeydown="if(event.key === 'Enter') search()">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    
                    <button onclick="search()" id="searchBtn" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-3 rounded-xl transition-all shadow-lg flex items-center justify-center gap-2">
                        Find Similar
                    </button>

                    <div id="resultsArea" class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
                        <!-- Results will appear here -->
                        <div class="text-center text-gray-500 mt-10">Results will appear here...</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        async function storeDocument() {
            const content = document.getElementById('documentContent').value;
            const btn = document.getElementById('storeBtn');
            const msg = document.getElementById('storeMessage');

            if (!content || content.length < 10) {
                alert('Please enter at least 10 characters.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin mr-2">⟳</span> Storing...';
            
            try {
                const res = await axios.post('{{ route('ai.vector.store') }}', { content });
                msg.textContent = 'Document stored successfully!';
                msg.className = 'text-center text-sm font-medium text-green-400 block';
                document.getElementById('documentContent').value = '';
                
                setTimeout(() => {
                    msg.className = 'hidden';
                }, 3000);

            } catch (error) {
                console.error(error);
                msg.textContent = 'Error storing document.';
                msg.className = 'text-center text-sm font-medium text-red-400 block';
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg> Store Document`;
            }
        }

        async function search() {
            const query = document.getElementById('searchQuery').value;
            const btn = document.getElementById('searchBtn');
            const resultsArea = document.getElementById('resultsArea');

            if (!query) return;

            btn.disabled = true;
            btn.innerHTML = '<span class="animate-spin mr-2">⟳</span> Searching...';

            try {
                const res = await axios.post('{{ route('ai.vector.search.api') }}', { query });
                const matches = res.data.results;

                if (matches.length === 0) {
                    resultsArea.innerHTML = '<div class="text-center text-gray-500 mt-10">No visible matches found.</div>';
                } else {
                    resultsArea.innerHTML = matches.map(match => `
                        <div class="bg-gray-900 border border-gray-700/50 p-4 rounded-xl hover:border-purple-500/50 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-mono bg-gray-800 text-gray-300 px-2 py-1 rounded">Score: ${(match.score * 100).toFixed(1)}%</span>
                            </div>
                            <p class="text-gray-300 text-sm leading-relaxed">${match.metadata && match.metadata.content ? match.metadata.content : 'No text content available'}</p>
                        </div>
                    `).join('');
                }

            } catch (error) {
                console.error(error);
                resultsArea.innerHTML = '<div class="text-center text-red-400 mt-10">Search failed. Check console.</div>';
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Find Similar';
            }
        }
    </script>
</x-app-layout>
