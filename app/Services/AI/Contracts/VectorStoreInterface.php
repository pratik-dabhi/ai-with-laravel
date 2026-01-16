<?php

namespace App\Services\AI\Contracts;

interface VectorStoreInterface
{
    public function upsert(string $id, array $vector, array $metadata = []): bool;
    
    public function search(array $vector, int $limit = 5): array;
}
