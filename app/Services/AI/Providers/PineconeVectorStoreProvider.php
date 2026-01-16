<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\VectorStoreInterface;
use Illuminate\Support\Facades\Http;

class PineconeVectorStoreProvider implements VectorStoreInterface
{
    protected string $host;
    protected string $apiKey;

    public function __construct()
    {
        $this->host = config('services.pinecone.host');
        $this->apiKey = config('services.pinecone.key');

        if (empty($this->host) || empty($this->apiKey)) {
            throw new \Exception('Pinecone configuration (host/key) is missing.');
        }
    }

    public function upsert(string $id, array $vector, array $metadata = []): bool
    {
        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("https://{$this->host}/vectors/upsert", [
            'vectors' => [
                [
                    'id' => $id,
                    'values' => $vector,
                    'metadata' => $metadata
                ]
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception('Pinecone Upsert Error: ' . $response->body());
        }

        return true;
    }

    public function search(array $vector, int $limit = 5): array
    {
        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("https://{$this->host}/query", [
            'vector' => $vector,
            'topK' => $limit,
            'includeMetadata' => true
        ]);

        if ($response->failed()) {
            throw new \Exception('Pinecone Search Error: ' . $response->body());
        }

        return $response->json()['matches'] ?? [];
    }
}
