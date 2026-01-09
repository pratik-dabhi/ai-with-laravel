# Laravel MCP Server Documentation: Chat Integration

This document provides a guide for the setup, architecture, and maintenance of the Chat MCP (Model Context Protocol) Server implemented in this Laravel application.

## 1. Prerequisites & Installation

The server utilizes the `laravel/mcp` package.

```bash
composer require laravel/mcp
```

## 2. Server Architecture

The MCP implementation follows a modular structure located in `app/Mcp`.

### Server Class: `App\Mcp\Servers\ChatServer`

The server class acts as a registry for tools and configurations.

- **Tools**: Registered in the `$tools` array.
- **Instructions**: Provides the LLM with context on how to use the available tools.

### Tools

Each tool is a separate class extending `Laravel\Mcp\Server\Tool`.

#### `ListChatsTool`

- **Purpose**: Lists recent chat sessions.
- **Output**: Returns a text summary of the last 20 chats (ID, Title, Message Count).
- **Key implementation**: Uses `Chat::withCount('messages')->latest()` for efficiency.

#### `GetChatMessagesTool`

- **Purpose**: Retrieves the full transcript of a specific chat.
- **Arguments**: Requires `chat_id` (Integer).
- **Input Schema**: Defined using the `JsonSchema $schema` factory in the `schema()` method.
- **Handle Method**: Parameters must be retrieved via the `Laravel\Mcp\Request $request` object.

## 3. Setup & Configuration

### Route Registration (`routes/ai.php`)

The server is exposed via a web endpoint:

```php
Mcp::web('/mcp/chats', ChatServer::class);
```

### Middleware & Security (`bootstrap/app.php`)

MCP requests are typically JSON-RPC POST requests. For local development and testing:

1. **CSRF Exemption**: You must exempt the MCP routes from CSRF verification.
2. **CORS Configuration**: If using the browser-based MCP Inspector, ensure `mcp/*` is added to your permitted CORS paths in `config/cors.php`.

### Environment (`.env`)

Ensure `APP_URL` is correctly set (including the port, e.g., `http://localhost:8000`) so the MCP Inspector can discover the server accurately.

## 4. Testing & Debugging

### MCP Inspector

The easiest way to test is using the Laravel MCP Inspector:

```bash
php artisan mcp:inspector /mcp/chats
```

Copy the generated URL (typically `http://localhost:8000/mcp/chats`) into the MCP Inspector UI at `http://localhost:6274`.

### Manual Testing (cURL)

You can test the JSON-RPC interface directly:

```bash
curl -X POST http://localhost:8000/mcp/chats \
-H "Content-Type: application/json" \
-d '{"jsonrpc":"2.0","id":1,"method":"tools/list"}'
```

## 5. Adding New Tools

1. Create a new class in `app/Mcp/Tools` extending `Tool`.
2. Implement the `handle()` method.
3. (Optional) Define input validation in `schema()`.
4. Register the new tool class in the `$tools` array within `ChatServer.php`.
