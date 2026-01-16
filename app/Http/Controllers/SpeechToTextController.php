<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AI\AiService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class SpeechToTextController extends Controller
{
    public function __construct(
        protected AiService $ai
    ) {}

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
            $transcript = $this->ai->transcriber()->transcribe($request->file('audio'));

            return response()->json([
                'transcript' => $transcript
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
