<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AI\AiService;
use Illuminate\Support\Facades\Log;

class TextToSpeechController extends Controller
{
    public function __construct(
        protected AiService $ai
    ) {}

    public function index() 
    {
        try {
            $voices = $this->ai->speech()->getVoices();
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
            
            $audioUrl = $this->ai->speech()->speak($text, $voiceId);

            return response()->json([
                'audio_url' => asset($audioUrl),
                'message' => 'Audio generated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('TextToSpeech Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
