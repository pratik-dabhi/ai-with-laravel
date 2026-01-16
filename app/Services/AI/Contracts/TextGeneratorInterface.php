<?php

namespace App\Services\AI\Contracts;

interface TextGeneratorInterface
{
    public function generate(string $prompt, array $options = []);
    
    public function stream(string $prompt, array $options = []);
}
