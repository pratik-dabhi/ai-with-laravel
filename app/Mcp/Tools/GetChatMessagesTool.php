<?php

namespace App\Mcp\Tools;

use App\Models\Chat;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetChatMessagesTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = 'Fetch the full message history for a specific chat conversation by its ID.';

    /**
     * Define the tool's input schema.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'chat_id' => $schema->integer()->description('The unique ID of the chat conversation.')->required(),
        ];
    }

    /**
     * Handle the tool call.
     */
    public function handle(\Laravel\Mcp\Request $request): Response
    {
        $chat_id = $request->get('chat_id');

        if (!$chat_id) {
            return Response::error("Missing chat_id parameter.");
        }

        $chat = Chat::with(['messages' => fn($query) => $query->oldest()])->find($chat_id);

        if (!$chat) {
            return Response::error("Chat history with ID {$chat_id} not found.");
        }

        if ($chat->messages->isEmpty()) {
            return Response::text("Chat \"{$chat->title}\" (ID: {$chat->id}) has no messages.");
        }

        $output = $chat->messages->map(function ($message) {
            $role = ucfirst($message->role);
            return "[{$message->created_at->toDateTimeString()}] {$role}: {$message->content}";
        })->implode("\n\n");

        return Response::text("Messages for Chat \"{$chat->title}\" (ID: {$chat->id}):\n\n" . $output);
    }
}
