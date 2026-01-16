<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\VectorStoreInterface;
use Illuminate\Support\Facades\Storage;

class JsonVectorStoreProvider implements VectorStoreInterface
{
    protected string $filePath = 'vectors.json';

    public function upsert(string $id, array $vector, array $metadata = []): bool
    {
        $data = $this->loadData();
        
        $data[$id] = [
            'id' => $id,
            'vector' => $vector,
            'metadata' => $metadata,
            'updated_at' => now()->timestamp
        ];

        Storage::put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
        
        return true;
    }

    public function search(array $vector, int $limit = 5): array
    {
        $data = $this->loadData();
        $results = [];

        foreach ($data as $item) {
            $similarity = $this->cosineSimilarity($vector, $item['vector']);
            $results[] = [
                'id' => $item['id'],
                'score' => $similarity,
                'metadata' => $item['metadata']
            ];
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, $limit);
    }

    protected function loadData(): array
    {
        if (!Storage::exists($this->filePath)) {
            return [];
        }

        return json_decode(Storage::get($this->filePath), true) ?? [];
    }

    protected function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        foreach ($vecA as $i => $val) {
            if (!isset($vecB[$i])) continue;
            
            $dotProduct += $val * $vecB[$i];
            $magnitudeA += $val * $val;
            $magnitudeB += $vecB[$i] * $vecB[$i];
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA * $magnitudeB == 0) {
            return 0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }
}
