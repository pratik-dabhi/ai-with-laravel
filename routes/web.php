<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\TextGenerationController;
use App\Http\Controllers\ImageGenerationController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', fn() => view('welcome'))->name('home');

// AI Text Generation Routes
Route::prefix('ai')->name('ai.')->group(function () {
    Route::post('text', [TextGenerationController::class, 'text'])->name('text');
    Route::get('stream', [TextGenerationController::class, 'stream'])->name('stream');
    Route::view('playground', 'ai.playground')->name('playground');
    Route::post('image', [ImageGenerationController::class, 'generate'])->name('image.generate');
    Route::get('image/status/{taskId}', [ImageGenerationController::class, 'status'])->name('image.status');
});

// Chat Resource Routes
Route::prefix('chats')->name('chats.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::post('/', [ChatController::class, 'store'])->name('store');
    Route::get('{chat}', [ChatController::class, 'show'])->name('show');
    Route::patch('{chat}', [ChatController::class, 'update'])->name('update');
    Route::delete('{chat}', [ChatController::class, 'destroy'])->name('destroy');
    Route::post('{chat}/messages', [ChatController::class, 'send'])->name('messages.store');
});
