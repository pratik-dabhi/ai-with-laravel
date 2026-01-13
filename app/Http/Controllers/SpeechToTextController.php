<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class SpeechToTextController extends Controller
{
    public function index()
    {
        return view('speech-to-text.index');
    }

    public function transcribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'audio' => ['required', 'file', 'mimes:mp3,wav,m4a,flac,ogg,webm', 'max:10240'], // 10MB limit
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $audioPath = $request->file('audio')->getPathname();
            $mimeType = $request->file('audio')->getMimeType();
            $apiKey = config('services.deepgram.key');

            if (!$apiKey) {
                return response()->json([
                    'error' => 'Deepgram API key is not configured.'
                ], 500);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $apiKey,
                'Content-Type' => $mimeType
            ])->withBody(file_get_contents($audioPath), $mimeType)
                ->post('https://api.deepgram.com/v1/listen?model=nova-3&smart_format=true');

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to connect to Deepgram API: ' . $response->body()
                ], 502);
            }

            $data = $response->json();
            $transcript = $data['results']['channels'][0]['alternatives'][0]['transcript'] ?? '';

            if (empty($transcript)) {
                return response()->json([
                    'error' => 'Check your audio content; No speech could be detected.'
                ], 422);
            }

            return response()->json([
                'transcript' => $transcript
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred during transcription: ' . $e->getMessage()
            ], 500);
        }
    }
}
