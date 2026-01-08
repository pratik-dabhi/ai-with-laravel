<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Playground (Prism + Ollama)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        textarea {
            white-space: pre-wrap;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-5xl mx-auto space-y-6">
        <h1 class="text-4xl font-bold text-center">
            Prism + Ollama (Mistral)
        </h1>

        <div class="bg-white p-4 rounded shadow space-y-3">
            <textarea id="prompt" rows="4" class="w-full border rounded p-2 outline-none" placeholder="Ask something..."></textarea>

            <div class="flex gap-3">
                <button onclick="generateText()" class="px-4 py-2 bg-emerald-600 text-white rounded">
                    Text
                </button>

                <button onclick="generateStream()" class="px-4 py-2 bg-blue-600 text-white rounded">
                    Stream
                </button>
            </div>
        </div>

        {{-- OUTPUT --}}
        <div class="bg-white p-4 rounded shadow">
            <label class="font-semibold">Output</label>
            <textarea id="output" rows="14" class="w-full border rounded p-2 bg-gray-50 outline-none" readonly></textarea>
        </div>
    </div>

    <script>
        const promptInput = document.getElementById('prompt');
        const output = document.getElementById('output');

        /* ---------- TEXT ---------- */
        async function generateText() {
            output.value = '';

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
        }

        /* ---------- STREAM ---------- */
        function generateStream() {
            output.value = '';

            const url = `/ai/stream?prompt=${encodeURIComponent(promptInput.value)}`;
            const evtSource = new EventSource(url);

            evtSource.onmessage = (event) => {
                output.value += event.data;
            };
            
            evtSource.addEventListener('text_delta', (event) => {
                const payload = JSON.parse(event.data);
                output.value += payload.delta;
            });

            evtSource.addEventListener('stream_end', () => {
                evtSource.close();
            });

            evtSource.onerror = () => {
                evtSource.close();
            };
        }
    </script>
</body>
</html>
