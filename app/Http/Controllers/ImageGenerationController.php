<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ImageGenerationController extends Controller
{
    public function index()
    {
        return view('ai.playground');
    }

    /**
     * Start the image generation process.
     */
    public function generate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'prompt' => ['required', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        $prompt = $request->input('prompt');
        $apiKey = config('services.freepik.key');

        if (!$apiKey) {
            return response()->json([
                'error' => 'Freepik API key is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'X-Freepik-API-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.freepik.com/v1/ai/mystic', [
                'prompt' => $prompt,
                'aspect_ratio' => 'square_1_1',
                'num_images' => 1,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to connect to Freepik API: ' . $response->body()
                ], 502);
            }

            $data = $response->json();

            if (isset($data['data']['status']) && $data['data']['status'] === 'CREATED') {
                return response()->json([
                    'status' => 'processing',
                    'task_id' => $data['data']['task_id']
                ]);
            }

            return response()->json([
                'error' => 'Unexpected response from Freepik: ' . ($data['message'] ?? 'Unknown error')
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred during image generation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check the status of an image generation task.
     */
    public function status(string $taskId): JsonResponse
    {
        $apiKey = config('services.freepik.key');

        try {
            $response = Http::withHeaders([
                'X-Freepik-API-Key' => $apiKey,
            ])->get('https://api.freepik.com/v1/ai/mystic/' . $taskId);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to check status: ' . $response->body()
                ], 502);
            }

            $data = $response->json();
            $status = $data['data']['status'] ?? 'UNKNOWN';

            if ($status === 'COMPLETED' && isset($data['data']['generated'][0])) {
                return response()->json([
                    'status' => 'completed',
                    'url' => $data['data']['generated'][0]
                ]);
            }

            if ($status === 'FAILED') {
                return response()->json([
                    'status' => 'failed',
                    'error' => 'Image generation failed.'
                ]);
            }

            return response()->json([
                'status' => 'processing',
                'message' => 'Image is still being generated...'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while checking status: ' . $e->getMessage()
            ], 500);
        }
    }
}
