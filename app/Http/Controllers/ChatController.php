<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::latest()->get();
        return view('chat.index', compact('chats'));
    }

    public function show(Chat $chat)
    {
        $chats = Chat::latest()->get();
        $chat->load('messages');
        return view('chat.index', compact('chats', 'chat'));
    }

    public function store(Request $request)
    {
        $chat = Chat::create([
            'title' => 'New Chat ' . now()->format('Y-m-d H:i'),
        ]);

        return redirect()->route('chats.show', $chat);
    }

    public function update(Request $request, Chat $chat)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $chat->update([
            'title' => $request->title,
        ]);

        return response()->json([
            'success' => true,
            'title' => $chat->title,
        ]);
    }

    public function destroy(Chat $chat)
    {
        $chat->delete();
        return redirect()->route('chats.index');
    }

    public function send(Request $request, Chat $chat)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        
        $chat->messages()->create([
            'role' => 'user',
            'content' => $request->content,
        ]);

        $messages = $chat->messages()->oldest()->get()->map(function ($message) {
            if ($message->role === 'user') {
                return new \Prism\Prism\ValueObjects\Messages\UserMessage($message->content);
            }
            return new \Prism\Prism\ValueObjects\Messages\AssistantMessage($message->content);
        })->toArray();


        $response = Prism::text()
            ->using(Provider::Ollama, 'mistral')
            ->withMessages($messages)
            ->withClientOptions(['timeout' => 120])
            ->asText();

        $message = $chat->messages()->create([
            'role' => 'assistant',
            'content' => $response->text,
        ]);

        // Auto-generate title from first user message
        if (str_starts_with($chat->title, 'New Chat')) {
            $titlePrompt = "Generate a short, concise title (3-5 words maximum) for a chat that starts with this message: \"{$request->content}\". Respond with ONLY the title, no quotes or extra text.";
            
            $titleResponse = Prism::text()
                ->using(Provider::Ollama, 'mistral')
                ->withPrompt($titlePrompt)
                ->withClientOptions(['timeout' => 30])
                ->asText();

            $chat->update([
                'title' => trim($titleResponse->text),
            ]);
        }

        return response()->json($message);
    }
}
