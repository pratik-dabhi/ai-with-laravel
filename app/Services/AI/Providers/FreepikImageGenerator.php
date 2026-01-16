<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\ImageGeneratorInterface;
use Illuminate\Support\Facades\Http;

class FreepikImageGenerator implements ImageGeneratorInterface
{
    protected string $baseUrl = 'https://api.freepik.com/v1/ai/mystic';

    protected function getApiKey()
    {
        $key = config('services.freepik.key');
        if (!$key) {
            throw new \Exception('Freepik API key is not configured.');
        }
        return $key;
    }

    public function generate(string $prompt, array $options = [])
    {
        $response = Http::withHeaders([
            'X-Freepik-API-Key' => $this->getApiKey(),
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl, [
            'prompt' => $prompt,
            'aspect_ratio' => $options['aspect_ratio'] ?? 'square_1_1',
            'num_images' => $options['num_images'] ?? 1,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to connect to Freepik API: ' . $response->body());
        }

        $data = $response->json();

        if (isset($data['data']['status']) && $data['data']['status'] === 'CREATED') {
            return [
                'status' => 'processing',
                'task_id' => $data['data']['task_id']
            ];
        }

        throw new \Exception('Unexpected response from Freepik: ' . ($data['message'] ?? 'Unknown error'));
    }

    public function checkStatus(string $taskId)
    {
        $response = Http::withHeaders([
            'X-Freepik-API-Key' => $this->getApiKey(),
        ])->get($this->baseUrl . '/' . $taskId);

        if ($response->failed()) {
            throw new \Exception('Failed to check status: ' . $response->body());
        }

        $data = $response->json();
        $status = $data['data']['status'] ?? 'UNKNOWN';

        if ($status === 'COMPLETED' && isset($data['data']['generated'][0])) {
            return [
                'status' => 'completed',
                'url' => $data['data']['generated'][0]
            ];
        }

        if ($status === 'FAILED') {
            return [
                'status' => 'failed',
                'error' => 'Image generation failed.'
            ];
        }

        return [
            'status' => 'processing',
            'message' => 'Image is still being generated...'
        ];
    }
}
