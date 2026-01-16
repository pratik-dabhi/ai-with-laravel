<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\TranscriberInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class DeepgramTranscriber implements TranscriberInterface
{
    protected string $baseUrl = 'https://api.deepgram.com/v1/listen';

    protected function getApiKey()
    {
        $key = config('services.deepgram.key');
        if (!$key) {
            throw new \Exception('Deepgram API key is not configured.');
        }
        return $key;
    }

    public function transcribe(UploadedFile $file, string $model = 'nova-3')
    {
        $audioPath = $file->getPathname();
        $mimeType = $file->getMimeType();

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->getApiKey(),
            'Content-Type' => $mimeType
        ])->withBody(file_get_contents($audioPath), $mimeType)
            ->post($this->baseUrl . "?model={$model}&smart_format=true");

        if ($response->failed()) {
            throw new \Exception('Failed to connect to Deepgram API: ' . $response->body());
        }

        $data = $response->json();
        $transcript = $data['results']['channels'][0]['alternatives'][0]['transcript'] ?? '';

        if (empty($transcript)) {
            throw new \Exception('Check your audio content; No speech could be detected.');
        }

        return $transcript;
    }
}
