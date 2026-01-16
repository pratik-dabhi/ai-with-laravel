<?php

namespace App\Services\AI\Contracts;

interface SpeechSynthesizerInterface
{
    public function getVoices();
    
    public function speak(string $text, string $voiceId = 'oliver'): string;
}
