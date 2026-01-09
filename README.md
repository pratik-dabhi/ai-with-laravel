# Laravel AI Integration

A robust Laravel 12.x foundation integrated with modern AI capabilities, providing a template for building conversational interfaces, Model Context Protocol (MCP) servers, and various AI-powered features.

## 🚀 Features

-   **[Conversational AI / Text Generation](docs/chatbot.md)**: Native multi-turn chat interfaces with history management.
-   **[Model Context Protocol (MCP)](docs/mcp.md)**: Built-in MCP server support to expose your application data securely to LLMs.

## 🛠 Tech Stack

-   **Framework**: Laravel 12.0+
-   **AI Abstraction**: [Prism PHP](https://prism-php.com/)
-   **Protocol**: [Model Context Protocol (MCP)](https://modelcontextprotocol.io/) via [Laravel MCP](https://github.com/laravel/mcp)
-   **Local LLM Runner**: [Ollama](https://ollama.com/) (default)
-   **Frontend**: Vite, Tailwind CSS, and Vanilla JS

## 📋 Pre-requisites

Before setting up the project, ensure you have the following installed:

1.  **PHP**: ^8.2
2.  **Composer**
3.  **Node.js & NPM**
4.  **Ollama**: [Download and install Ollama](https://ollama.com/download)
5.  **Models**: Pull the required models for the default configuration:
    ```bash
    ollama pull mistral
    ollama pull whisper
    ```

## ⚙️ Setup Instructions

Follow these steps to get your project up and running:

### 1. Clone & Install

Run the built-in setup script which handles dependency installation, environment setup, and migrations:

```bash
composer setup
```

This script will:

-   Install Composer dependencies.
-   Create a `.env` file (if not exists).
-   Generate an application key.
-   Run database migrations.
-   Install NPM dependencies.
-   Build frontend assets.

### 2. Configure Environment

Ensure your `.env` file reflects your AI provider configurations. By default, this project uses Ollama:

```env
# Example AI Configuration
PRISM_OLLAMA_URL=http://localhost:11434
```

### 3. Start Development Server

You can start all necessary services (Server, Queue, Vite, etc.) using the shortcut command:

```bash
composer dev
```

## 🔌 Model Context Protocol (MCP)

This project includes a built-in MCP server at `/mcp/chats`. You can connect this to MCP-compatible clients (like Claude Desktop) to allow them to interact with your application's chat data.

-   **MCP Endpoint**: `http://localhost:8000/mcp/chats`
-   **Exposed Tools**: `list_chats`, `get_chat_messages`

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
