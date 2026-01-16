<?php

namespace App\Http\Controllers;

use App\Services\AI\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VectorSearchController extends Controller
{
    public function __construct(
        protected AiService $ai
    ) {}

    public function index()
    {
        return view('ai.vector-search.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:10',
        ]);

        $content = $request->input('content');
        $id = (string) Str::uuid();
        
        $vector = $this->ai->embedding()->embed($content);

        $this->ai->vectorStore()->upsert($id, $vector, [
            'content' => $content,
            'source' => 'user_input',
            'created_at' => now()->toIso8601String(),
        ]);

        return response()->json(['message' => 'Document processed and stored.', 'id' => $id]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $results = $this->ai->search($request->input('query'));

        return response()->json(['results' => $results]);
    }
}
