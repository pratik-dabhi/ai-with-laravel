# AI Chatbot & Text Generation Documentation

This document explains the implementation of the AI features in this Laravel application, including conversational chat, context management, and auto-generation capabilities.

## 1. Technology Stack

-   **AI Integration**: [Prism for Laravel](https://prism-php.com/) (`prism-php/prism`).
-   **Provider**: [Ollama](https://ollama.com/) (Running locally).
-   **Model**: `mistral`.
-   **Database**: SQLite (storing `chats` and `messages`).

## 2. Core Features

### Conversational Chat (`ChatController@send`)

The chatbot maintains context by passing the entire conversation history to the AI model.

-   **Context Management**: Previous messages are fetched from the database, mapped to Prism's `UserMessage` and `AssistantMessage` objects, and passed to the AI via `.withMessages($messages)`.
-   **Async Execution**: The frontend sends messages via Axios, and the backend returns the AI response as JSON.

### Auto-Generated Titles

When a new chat is started (with the default "New Chat" title), the system automatically generates a concise title based on the first user message.

-   **Logic**: A secondary, fast AI call is made with a specific prompt to summarize the initial message into 3-5 words.
-   **Update**: The `chats` table is updated, and the sidebar reflects the new title immediately.

### Chat Management

-   **Renaming**: Users can custom-rename chats. The UI uses a custom themed modal to prevent browser `prompt()` interruptions.
-   **Deletion**: Chats can be deleted along with their entire message history (enforced by database foreign key cascading).

## 3. Implementation Details

### Database Schema

-   **`chats`**: Stores the conversation session (id, title, timestamps).
-   **`messages`**: Stores individual messages (id, chat_id, role, content, timestamps). Role is either `user` or `assistant`.

### Route Structure

The application follows RESTful conventions:

-   `GET /chats`: List all chats.
-   `GET /chats/{chat}`: View a specific chat.
-   `POST /chats`: Create a new chat session.
-   `PATCH /chats/{chat}`: Rename a chat.
-   `DELETE /chats/{chat}`: Delete a chat.
-   `POST /chats/{chat}/messages`: Send a message and get an AI response.

## 4. UI/UX Design

-   **Dark Mode**: A modern, premium dark-themed interface using Tailwind CSS.
-   **Navigation Rail**: A fixed left-side sidebar for quick navigation between chats and other features.
-   **Custom Modals**: Themed modals for confirmation and input, ensuring a consistent aesthetic throughout the app.

## 5. Extending the AI

To change the model or provider, update the `ChatController.php`:

```php
$response = Prism::text()
    ->using(Provider::Ollama, 'your-new-model') // Change model here
    ->withMessages($messages)
    ->asText();
```
