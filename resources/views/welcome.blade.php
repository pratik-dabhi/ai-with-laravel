<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
</head>
<body class="h-full flex flex-col items-center justify-center p-6 text-center text-white font-sans antialiased">
    
    <div class="max-w-xl w-full space-y-8 animate-fade-in">
        <div class="space-y-4">
            <div class="bg-blue-600/20 p-4 rounded-full w-20 h-20 mx-auto flex items-center justify-center ring-1 ring-blue-500/50 shadow-[0_0_30px_rgba(37,99,235,0.3)]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-blue-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21" />
                </svg>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-bold tracking-tight bg-gradient-to-br from-white to-gray-400 bg-clip-text text-transparent">
                AI Assistant
            </h1>
            
            <p class="text-lg text-gray-400 max-w-md mx-auto leading-relaxed">
                Experience the power of local AI. Chat with Mistral via Ollama, stream responses, and explore possibilities.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('chats.index') }}" class="group relative bg-gray-800 hover:bg-gray-750 border border-gray-700 hover:border-blue-500/50 rounded-2xl p-6 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1">
                <div class="flex items-center gap-4 mb-2">
                    <div class="bg-blue-500/10 p-2 rounded-lg group-hover:bg-blue-500/20 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                    </div>
                    <span class="font-semibold text-lg">Chat</span>
                </div>
                <p class="text-sm text-gray-400 text-left">Conversational interface with context awareness and instant replies.</p>
            </a>

            <a href="/ai/playground" class="group relative bg-gray-800 hover:bg-gray-750 border border-gray-700 hover:border-emerald-500/50 rounded-2xl p-6 transition-all duration-300 hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-1">
                <div class="flex items-center gap-4 mb-2">
                    <div class="bg-emerald-500/10 p-2 rounded-lg group-hover:bg-emerald-500/20 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-emerald-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                        </svg>
                    </div>
                    <span class="font-semibold text-lg">Playground</span>
                </div>
                <p class="text-sm text-gray-400 text-left">Experiment with different prompts and models directly.</p>
            </a>
        </div>
        
        <div class="text-sm text-gray-600">
            Powered by Laravel + Prism + Ollama
        </div>
    </div>
</body>
</html>
