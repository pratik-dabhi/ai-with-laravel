<?php

namespace App\Http\Controllers;

use App\Services\AI\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisionController extends Controller
{
    public function __construct(
        protected AiService $ai
    ) {}

    public function index()
    {
        return view('ai.vision.index');
    }

    public function analyze(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image' => ['required', 'image', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $description = $this->ai->vision()->describe($request->file('image'));

            return response()->json([
                'description' => $description
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
