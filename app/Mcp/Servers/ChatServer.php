<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetChatMessagesTool;
use App\Mcp\Tools\ListChatsTool;
use Laravel\Mcp\Server;

class ChatServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Chat Details Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '1.0.0';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = 'This server provides access to user chat conversations and their message history. Use list_chats to find recent sessions and get_chat_messages to read the content of a specific session.';

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        ListChatsTool::class,
        GetChatMessagesTool::class,
    ];
}
