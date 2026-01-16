<?php

namespace App\Services\AI\Providers;

use App\Services\AI\Contracts\SpeechSynthesizerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SpeechifySpeechSynthesizer implements SpeechSynthesizerInterface
{
    protected string $baseUrl = 'https://api.sws.speechify.com/v1';

    protected function getApiKey()
    {
        $key = config('services.speechify.key');
        if (empty($key)) {
            throw new \Exception('Speechify API key is not configured.');
        }
        return $key;
    }

    protected function getClient()
    {
        return Http::withOptions([
            'force_ip_resolve' => 'v4',
            'connect_timeout' => 10,
            'timeout' => 30,
            'verify' => false,
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $this->getApiKey(),
            'Content-Type' => 'application/json'
        ]);
    }

    public function getVoices()
    {
        $response = $this->getClient()->get("{$this->baseUrl}/voices");
        
        if ($response->failed()) {
            throw new \Exception('Failed to fetch voices: ' . $response->body());
        }

        return $response->json();
    }

    public function speak(string $text, string $voiceId = 'oliver'): string
    {
        $response = $this->getClient()->post("{$this->baseUrl}/audio/speech", [
            "input" => $text,
            "voice_id" => $voiceId,
            'format' => 'mp3'
        ]);

        if ($response->failed()) {
            throw new \Exception('Speechify API Error: ' . $response->body());
        }

        $data = $response->json();

        if (isset($data['audio_data'])) {
            $audioBinary = base64_decode($data['audio_data']);
            $filename = 'speechify_' . time() . '_' . Str::random(10) . '.mp3';
            
            Storage::disk('public')->put($filename, $audioBinary);
            
            return 'storage/' . $filename;
        }

        throw new \Exception('Failed to generate audio from external service.');
    }
}
