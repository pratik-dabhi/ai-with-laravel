<?php

namespace App\Services\AI\Contracts;

interface ImageGeneratorInterface
{
    public function generate(string $prompt, array $options = []);
    
    public function checkStatus(string $taskId);
}
