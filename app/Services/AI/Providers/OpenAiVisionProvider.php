<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\VisionInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class OpenAiVisionProvider implements VisionInterface
{
    protected string $baseUrl = 'https://api.openai.com/v1/chat/completions';

    protected function getApiKey()
    {
        $key = config('services.openai.key'); 
        
        if (empty($key)) {
            throw new \Exception('OpenAI API key is not configured.');
        }
        return $key;
    }

    public function describe(UploadedFile $image): string
    {
        $base64Image = base64_encode(file_get_contents($image->getRealPath()));
        $mimeType = $image->getMimeType(); // e.g., image/jpeg

        $response = Http::withToken($this->getApiKey())
            ->post($this->baseUrl, [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Describe this image in detail'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$base64Image}"
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 300
            ]);

        if ($response->failed()) {
            $error = $response->json()['error']['message'] ?? $response->body();
            throw new \Exception('OpenAI Vision API Error: ' . $error);
        }

        return $response->json()['choices'][0]['message']['content'] ?? 'No description generated.';
    }
}
