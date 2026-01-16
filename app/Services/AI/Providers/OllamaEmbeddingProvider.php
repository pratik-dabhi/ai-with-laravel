<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\EmbeddingInterface;
use Illuminate\Support\Facades\Http;

class OllamaEmbeddingProvider implements EmbeddingInterface
{
    protected string $baseUrl;

    public function __construct()
    {
        // Use the Prism Ollama URL or default to localhost
        $this->baseUrl = env('PRISM_OLLAMA_URL', 'http://localhost:11434');
    }

    public function embed(string $text): array
    {
        // Using 'nomic-embed-text' as it is smaller and optimized for embeddings.
        // User must run `ollama pull nomic-embed-text`
        $model = env('AI_OLLAMA_EMBEDDING_MODEL', 'nomic-embed-text');

        $response = Http::post("{$this->baseUrl}/api/embeddings", [
            'model' => $model,
            'prompt' => $text, // Ollama API uses 'prompt' for embeddings
        ]);

        if ($response->failed()) {
            throw new \Exception('Ollama Embedding Error: ' . $response->body());
        }

        return $response->json()['embedding'];
    }
}
