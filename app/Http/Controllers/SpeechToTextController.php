<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpeechToTextController extends Controller
{
    public function transcribe(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3,m4a,ogg',
        ]);

        $audioPath = $request->file('audio')->getPathname();
        $audioData = base64_encode(file_get_contents($audioPath));

        $response = Http::timeout(300)
            ->post('http://localhost:11434/api/generate', [
                'model' => 'whisper',
                'prompt' => 'Transcribe this audio',
                'audio' => $audioData,
            ]);

        return response()->json([
            'text' => $response->json('response'),
        ]);
    }
}
