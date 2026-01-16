# Laravel AI Integration

A robust Laravel 12.x foundation integrated with modern AI capabilities, featuring a modular "Switchable Provider" architecture for Text, Image, Speech, and Vision.

## 🚀 Features

-   **Conversational AI / Text Generation**: Native multi-turn chat interfaces powered by **Ollama (Mistral)** by default.
-   **Image Generation**: Generate images using **Freepik Mystic** (switchable).
-   **Speech to Text**: Transcribe audio using **Deepgram** (switchable).
-   **Text to Speech**: Lifelike speech synthesis using **Speechify** (switchable).
-   **Image Understanding (Vision)**: Analyze images using **OpenAI GPT-4o** (switchable).
-   **Semantic Search (Vector DB)**: RAG capabilities using **OpenAI Embeddings** + **Pinecone** (switchable).
-   **Model Context Protocol (MCP)**: Built-in MCP server support.

## 🛠 Tech Stack

-   **Framework**: Laravel 12.0+
-   **Architecture**: Service/Provider Pattern with Strict Contracts
-   **AI Abstraction**: [Prism PHP](https://prism-php.com/) (for Text)
-   **Drivers**:
    -   Text: **Ollama** (Default)
    -   Image: **Freepik**
    -   Transcription: **Deepgram**
    -   Speech: **Speechify**
    -   Vision: **OpenAI**
-   **Frontend**: Vite, Tailwind CSS, Vanilla JS

## 📋 Pre-requisites & Setup

1.  **PHP 8.2+**, **Composer**, **Node.js**.
2.  **Ollama**: Installed and running locally (`ollama pull mistral`).
3.  **Clone & Install**:
    ```bash
    composer setup
    ```

## ⚙️ Configuration & Environment Variables

The application uses a **Switchable Provider** system controlled by `config/ai.php`. You can define which driver to use for each capability in your `.env` file.

### Required `.env` Variables

To enable all features, add these keys to your `.env` file:

```env
# --- AI Drivers Configuration ---
# Options: prism, freepik, deepgram, speechify, openai
AI_TEXT_DRIVER=prism
AI_IMAGE_DRIVER=freepik
AI_TRANSCRIPTION_DRIVER=deepgram
AI_SPEECH_DRIVER=speechify
AI_VISION_DRIVER=openai
AI_EMBEDDING_DRIVER=ollama
AI_VECTOR_STORE_DRIVER=json

# --- API Keys ---

# Text Generation (Ollama)
PRISM_OLLAMA_URL=http://localhost:11434
# Optional: Change embedding model (default: nomic-embed-text)
AI_OLLAMA_EMBEDDING_MODEL=nomic-embed-text

# Image Generation (Freepik)
FREEPIK_API_KEY=your_freepik_api_key_here

# Speech to Text (Deepgram)
DEEPGRAM_API_KEY=your_deepgram_api_key_here

# Text to Speech (Speechify)
SPEECHIFY_API_KEY=your_speechify_api_key_here

# Vision (OpenAI)
OPENAI_API_KEY=your_openai_api_key_here

# Vector Search (Pinecone)
PINECONE_API_KEY=your_pinecone_api_key_here
PINECONE_HOST=your_index_host_url (e.g. index-name-xyz.svc.pinecone.io)
```

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
