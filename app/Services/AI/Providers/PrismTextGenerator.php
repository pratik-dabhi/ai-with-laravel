<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\TextGeneratorInterface;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;

class PrismTextGenerator implements TextGeneratorInterface
{
    protected Provider $provider = Provider::Ollama;
    protected string $model = 'mistral';

    public function generate(string $prompt, array $options = [])
    {
        return Prism::text()
            ->using($this->provider, $this->model)
            ->withPrompt($prompt)
            ->withClientOptions(['timeout' => $options['timeout'] ?? 120])
            ->asText();
    }

    public function stream(string $prompt, array $options = [])
    {
        return Prism::text()
            ->using($this->provider, $this->model)
            ->withPrompt($prompt)
            ->withClientOptions([
                'timeout' => $options['timeout'] ?? 0,
            ])
            ->asEventStreamResponse();
    }
}
