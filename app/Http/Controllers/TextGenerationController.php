<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AI\AiService;
use Symfony\Component\HttpFoundation\Response;

class TextGenerationController extends Controller
{
    public function __construct(
        protected AiService $ai
    ) {}

    public function text(Request $request): Response
    {
        $request->validate([
            'prompt' => 'required|string'
        ]);

        $response = $this->ai->text()->generate($request->prompt);

        return response()->json([
            'content' => $response->text,
        ]);
    }

    public function stream(Request $request): Response
    {
        $request->validate([
            'prompt' => 'required|string',
        ]);

        return $this->ai->text()->stream($request->prompt);
    }
}
