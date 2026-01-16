<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\EmbeddingInterface;
use Illuminate\Support\Facades\Http;

class OpenAiEmbeddingProvider implements EmbeddingInterface
{
    protected string $baseUrl = 'https://api.openai.com/v1/embeddings';

    protected function getApiKey()
    {
        $key = config('services.openai.key'); 
        if (empty($key)) {
            throw new \Exception('OpenAI API key is not configured.');
        }
        return $key;
    }

    public function embed(string $text): array
    {
        $response = Http::withToken($this->getApiKey())
            ->post($this->baseUrl, [
                'model' => 'text-embedding-3-small',
                'input' => $text,
            ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI Embedding Error: ' . $response->body());
        }

        return $response->json()['data'][0]['embedding'];
    }
}
