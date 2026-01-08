<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use Symfony\Component\HttpFoundation\Response;

class TextGenerationController extends Controller
{
    protected Provider $provider = Provider::Ollama;
    protected string $model = 'mistral'; 

    public function text(Request $request): Response
    {
        $request->validate([
            'prompt' => 'required|string'
        ]);

        $response = Prism::text()
            ->using($this->provider, $this->model)
            ->withPrompt($request->prompt)
            ->withClientOptions(['timeout' => 120])
            ->asText();

        return response()->json([
            'content' => $response->text,
        ]);
    }

    public function stream(Request $request): Response
    {
        $request->validate([
            'prompt' => 'required|string',
        ]);

        return Prism::text()
            ->using($this->provider, $this->model)
            ->withPrompt($request->prompt)
            ->withClientOptions([
                'timeout' => 0, 
            ])
            ->asEventStreamResponse();
    }

}
