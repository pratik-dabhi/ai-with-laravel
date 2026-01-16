<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AI\AiService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ImageGenerationController extends Controller
{
    public function __construct(
        protected AiService $ai
    ) {}

    public function index()
    {
        return view('ai.playground');
    }

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

        try {
            $result = $this->ai->image()->generate($request->input('prompt'));
            
            if (isset($result['status']) && $result['status'] === 'processing') {
                 return response()->json($result);
            }
             
            return response()->json(['error' => 'Unknown error'], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function status(string $taskId): JsonResponse
    {
        try {
            $result = $this->ai->image()->checkStatus($taskId);
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while checking status: ' . $e->getMessage()
            ], 500);
        }
    }
}
