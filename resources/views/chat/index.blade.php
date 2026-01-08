<x-app-layout>
    <x-slot name="sidebar">
        <div class="p-4 border-b border-gray-700">
            <form action="{{ route('chats.store') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg px-4 py-2 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Chat
                </button>
            </form>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-2">
            @foreach($chats as $c)
                <div class="group relative flex items-center gap-2 p-3 rounded-lg hover:bg-gray-700 transition-colors {{ isset($chat) && $chat->id === $c->id ? 'bg-gray-700' : '' }}">
                    <a href="{{ route('chats.show', $c) }}" class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate chat-title" data-chat-id="{{ $c->id }}">{{ $c->title }}</div>
                    </a>
                    
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <!-- Rename Button -->
                        <button 
                            onclick="renameChat({{ $c->id }})" 
                            class="p-1 hover:bg-gray-600 rounded transition-colors"
                            title="Rename chat"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-400 hover:text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                        
                        <!-- Delete Button -->
                        <button 
                            onclick="showDeleteModal({{ $c->id }})"
                            class="p-1 hover:bg-red-600 rounded transition-colors"
                            title="Delete chat"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-400 hover:text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </x-slot>

    @if(isset($chat))
        <!-- Header -->
        <div class="md:hidden flex items-center p-4 border-b border-gray-700 bg-gray-800">
             <form action="{{ route('chats.store') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </button>
            </form>
            <span class="ml-4 font-medium">{{ $chat->title }}</span>
        </div>

        <!-- Messages -->
        <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-6 scroll-smooth">
            @foreach($chat->messages as $message)
                <div class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-3xl rounded-2xl px-6 py-4 {{ $message->role === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-100' }}">
                        <p class="whitespace-pre-wrap leading-relaxed">{{ $message->content }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Input -->
        <div class="p-4 border-t border-gray-700 bg-gray-900">
            <div class="max-w-4xl mx-auto relative">
                <form id="chat-form" class="relative">
                    <textarea 
                        id="message-input"
                        class="w-full bg-gray-800 text-white rounded-xl pl-4 pr-12 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none overflow-hidden"
                        placeholder="Type a message..."
                        rows="1"
                        oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                    ></textarea>
                    <button type="submit" class="absolute right-3 bottom-3 text-blue-500 hover:text-blue-400 p-1">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        @push('scripts')
        <script>
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const messagesContainer = document.getElementById('messages-container');
            const chatId = {{ $chat->id }};

            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            scrollToBottom();

            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const content = messageInput.value.trim();
                if (!content) return;

                // Clear input
                messageInput.value = '';
                messageInput.style.height = 'auto';

                // Append user message immediately
                appendMessage('user', content);

                try {
                    // Show loading indicator
                    const loadingId = appendLoading();
                    scrollToBottom();

                    const response = await axios.post(`/chats/${chatId}/messages`, {
                        content: content
                    });

                    // Remove loading and append assistant message
                    removeLoading(loadingId);
                    appendMessage('assistant', response.data.content);
                    
                    // Refresh page to update chat title if it was auto-generated
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                } catch (error) {
                    console.error('Error:', error);
                    alert('Something went wrong. Please try again.');
                }
            });

            function appendMessage(role, content) {
                const div = document.createElement('div');
                div.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;
                
                const bubble = document.createElement('div');
                bubble.className = `max-w-3xl rounded-2xl px-6 py-4 ${role === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-100'}`;
                
                const p = document.createElement('p');
                p.className = 'whitespace-pre-wrap leading-relaxed';
                p.textContent = content;
                
                bubble.appendChild(p);
                div.appendChild(bubble);
                messagesContainer.appendChild(div);
                scrollToBottom();
            }

            function appendLoading() {
                const id = 'loading-' + Date.now();
                const div = document.createElement('div');
                div.id = id;
                div.className = 'flex justify-start';
                div.innerHTML = `
                    <div class="bg-gray-700 text-gray-100 rounded-2xl px-6 py-4">
                        <div class="flex space-x-2">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                `;
                messagesContainer.appendChild(div);
                return id;
            }

            function removeLoading(id) {
                const el = document.getElementById(id);
                if (el) el.remove();
            }
        </script>
        @endpush
    @else
        <div class="flex-1 flex flex-col items-center justify-center p-8 text-center h-full">
            <div class="bg-gray-800 p-4 rounded-full mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">Welcome to AI Chat</h2>
            <p class="text-gray-400 mb-8 max-w-md">Start a new conversation to chat with the AI assistant powered by Ollama.</p>
            <form action="{{ route('chats.store') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-medium rounded-lg px-6 py-3 transition-colors shadow-lg shadow-blue-500/20">
                    Start New Conversation
                </button>
            </form>
        </div>
    @endif

    <!-- Custom Modal -->
    <div id="modal-overlay" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div id="modal-content" class="bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full border border-gray-700 transform transition-all">
            <!-- Modal will be populated by JavaScript -->
        </div>
    </div>

    @push('scripts')
    <script>
        const modalOverlay = document.getElementById('modal-overlay');
        const modalContent = document.getElementById('modal-content');

        // Close modal on overlay click
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modalOverlay.classList.contains('hidden')) {
                closeModal();
            }
        });

        function closeModal() {
            modalOverlay.classList.add('hidden');
            modalContent.innerHTML = '';
        }

        // Rename Chat Modal
        async function renameChat(chatId) {
            // Get current title from DOM
            const titleElement = document.querySelector(`.chat-title[data-chat-id="${chatId}"]`);
            const currentTitle = titleElement ? titleElement.textContent : '';
            
            modalContent.innerHTML = `
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-500/10 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white">Rename Chat</h3>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Chat Title</label>
                        <input 
                            type="text" 
                            id="rename-input" 
                            value="${currentTitle.replace(/"/g, '&quot;')}"
                            class="w-full bg-gray-900 border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter chat title"
                        />
                    </div>
                    
                    <div class="flex gap-3 justify-end">
                        <button 
                            onclick="closeModal()" 
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium"
                        >
                            Cancel
                        </button>
                        <button 
                            onclick="submitRename(${chatId})" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-colors font-medium"
                        >
                            Save
                        </button>
                    </div>
                </div>
            `;
            
            modalOverlay.classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('rename-input').focus();
                document.getElementById('rename-input').select();
            }, 100);
        }

        async function submitRename(chatId) {
            const newTitle = document.getElementById('rename-input').value.trim();
            if (!newTitle) return;

            try {
                const response = await axios.patch(`/chats/${chatId}`, {
                    title: newTitle
                });

                if (response.data.success) {
                    const titleElement = document.querySelector(`.chat-title[data-chat-id="${chatId}"]`);
                    if (titleElement) {
                        titleElement.textContent = newTitle;
                    }
                    closeModal();
                }
            } catch (error) {
                console.error('Error renaming chat:', error);
                alert('Failed to rename chat. Please try again.');
            }
        }

        // Delete Chat Modal
        function showDeleteModal(chatId) {
            // Get current title from DOM
            const titleElement = document.querySelector(`.chat-title[data-chat-id="${chatId}"]`);
            const chatTitle = titleElement ? titleElement.textContent : 'this chat';
            
            modalContent.innerHTML = `
                <div class="p-6">
                    <div class="flex justify-center items-center gap-3 mb-4">
                        <div class="bg-red-500/10 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    
                    <p class="text-gray-300 mb-2 text-center">Are you sure you want to delete this chat <span class="font-semibold">${chatTitle}</span>?</p>
                    <div class="flex gap-3 justify-center mt-5">
                        <button 
                            onclick="closeModal()" 
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium"
                        >
                            Cancel
                        </button>
                        <button 
                            onclick="confirmDelete(${chatId})" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition-colors font-medium"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            `;
            
            modalOverlay.classList.remove('hidden');
        }

        async function submitRename(chatId) {
            const newTitle = document.getElementById('rename-input').value.trim();
            if (!newTitle) return;

            try {
                const response = await axios.patch(`/chats/${chatId}`, {
                    title: newTitle
                });

                if (response.data.success) {
                    const titleElement = document.querySelector(`.chat-title[data-chat-id="${chatId}"]`);
                    if (titleElement) {
                        titleElement.textContent = newTitle;
                    }
                    closeModal();
                }
            } catch (error) {
                console.error('Error renaming chat:', error);
                alert('Failed to rename chat. Please try again.');
            }
        }

        // Delete Chat Modal
        function showDeleteModal(chatId) {
            // Get current title from DOM
            const titleElement = document.querySelector(`.chat-title[data-chat-id="${chatId}"]`);
            const chatTitle = titleElement ? titleElement.textContent : 'this chat';
            
            modalContent.innerHTML = `
                <div class="p-6">
                    <div class="flex justify-center items-center gap-3 mb-4">
                        <div class="bg-red-500/10 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    
                    <p class="text-gray-300 mb-2 text-center">Are you sure you want to delete this chat <span class="font-semibold">${chatTitle}</span>?</p>
                    <div class="flex gap-3 justify-center mt-5">
                        <button 
                            onclick="closeModal()" 
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium"
                        >
                            Cancel
                        </button>
                        <button 
                            onclick="confirmDelete(${chatId})" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white rounded-lg transition-colors font-medium"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            `;
            
            modalOverlay.classList.remove('hidden');
        }

        async function confirmDelete(chatId) {
            try {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/chats/${chatId}`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            } catch (error) {
                console.error('Error deleting chat:', error);
                alert('Failed to delete chat. Please try again.');
            }
        }
    </script>
    @endpush
</x-app-layout>
