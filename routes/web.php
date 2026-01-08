<?php

use App\Http\Controllers\TextGenerationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/* Text Generation */
Route::post('/ai/text', [TextGenerationController::class, 'text']);
Route::get('/ai/stream', [TextGenerationController::class, 'stream']);
Route::view('/ai/playground', 'ai.playground');

