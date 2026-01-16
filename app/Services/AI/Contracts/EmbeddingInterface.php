<?php

namespace App\Services\AI\Contracts;

interface EmbeddingInterface
{
    public function embed(string $text): array;
}
