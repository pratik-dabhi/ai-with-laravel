<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TextToSpeechController extends Controller
{
    public function index() 
    {
        $apiKey = config('services.speechify.key');
        
        try {
             $response = Http::withOptions([
                'force_ip_resolve' => 'v4',
                'connect_timeout' => 10,
                'timeout' => 30,
                'verify' => false,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->get('https://api.sws.speechify.com/v1/voices');
            
            $voices = $response->json();
            
            return view('text-to-speech.index', ['voices' => $voices]);
            
        } catch (\Exception $e) {
             Log::error('Failed to fetch voices: ' . $e->getMessage());
             return view('text-to-speech.index', ['voices' => []]);
        }
    }

    public function speech(Request $request)
    {
        try {
            $validated = $request->validate([
                'text' => ['required', 'string', 'max:5000'],
                'voice_id' => ['nullable', 'string'],
            ]);

            $text = $validated['text'];
            $voiceId = $validated['voice_id'] ?? 'oliver'; 
            $apiKey = config('services.speechify.key');

            if (empty($apiKey)) {
                return response()->json(['error' => 'Speechify API key is not configured.'], 500);
            }

            $response = Http::withOptions([
                'force_ip_resolve' => 'v4',
                'connect_timeout' => 10,
                'timeout' => 30,
                'verify' => false, 
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.sws.speechify.com/v1/audio/speech', [
                "input" => $text,
                "voice_id" => $voiceId,
                'format' => 'mp3'
            ]);

            \Log::info('$response', [$response]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['audio_data'])) {
                    $audioBinary = base64_decode($data['audio_data']);
                    $filename = 'speechify_' . time() . '_' . Str::random(10) . '.mp3';
                    
                    Storage::disk('public')->put($filename, $audioBinary);
                    
                    return response()->json([
                        'audio_url' => asset('storage/' . $filename),
                        'message' => 'Audio generated successfully'
                    ]);
                }
            }

            Log::error('Speechify API Error: ' . $response->body());
            return response()->json(['error' => 'Failed to generate audio from external service.'], 500);

        } catch (\Exception $e) {
            Log::error('TextToSpeech Error: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}
