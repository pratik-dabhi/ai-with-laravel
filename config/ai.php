<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Drivers
    |--------------------------------------------------------------------------
    |
    | Here you can define the default drivers regarding the AI capabilities.
    | feel free to add better options here.
    |
    | Supported: "prism", "freepik", "deepgram", "speechify"
    |
    */

    'text' => env('AI_TEXT_DRIVER', 'prism'),
    
    'image' => env('AI_IMAGE_DRIVER', 'freepik'),
    
    'transcription' => env('AI_TRANSCRIPTION_DRIVER', 'deepgram'),
    
    'speech' => env('AI_SPEECH_DRIVER', 'speechify'),

    'vision' => env('AI_VISION_DRIVER', 'openai'),

    'embedding' => env('AI_EMBEDDING_DRIVER', 'ollama'),
    
    'vector_store' => env('AI_VECTOR_STORE_DRIVER', 'json'),
];
