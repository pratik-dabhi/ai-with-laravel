<?php

namespace App\Mcp\Tools;

use App\Models\Chat;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ListChatsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'List all recent chat conversations with their titles and message counts.';

    /**
     * Handle the tool call.
     */
    public function handle(): Response
    {
        $chats = Chat::withCount('messages')->latest()->take(20)->get();

        if ($chats->isEmpty()) {
            return Response::text('No chats found.');
        }

        $output = $chats->map(function ($chat) {
            return "- ID: {$chat->id} | Title: {$chat->title} | Messages: {$chat->messages_count} | Created: {$chat->created_at->toDateTimeString()}";
        })->implode("\n");

        return Response::text("Recent Chat Conversations:\n" . $output);
    }
}
