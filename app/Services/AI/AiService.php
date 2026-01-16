<?php

namespace App\Services\AI;

class AiService
{
    public function text(): Contracts\TextGeneratorInterface
    {
        $driver = config('ai.text', 'prism');
        
        return match ($driver) {
            'prism' => new Providers\PrismTextGenerator(),
            default => throw new \Exception("Unknown text driver: {$driver}"),
        };
    }

    public function image(): Contracts\ImageGeneratorInterface
    {
        $driver = config('ai.image', 'freepik');
        
        return match ($driver) {
            'freepik' => new Providers\FreepikImageGenerator(),
            default => throw new \Exception("Unknown image driver: {$driver}"),
        };
    }

    public function transcriber(): Contracts\TranscriberInterface
    {
        $driver = config('ai.transcription', 'deepgram');
        
        return match ($driver) {
            'deepgram' => new Providers\DeepgramTranscriber(),
            default => throw new \Exception("Unknown transcription driver: {$driver}"),
        };
    }

    public function speech(): Contracts\SpeechSynthesizerInterface
    {
        $driver = config('ai.speech', 'speechify');
        
        return match ($driver) {
            'speechify' => new Providers\SpeechifySpeechSynthesizer(),
            default => throw new \Exception("Unknown speech driver: {$driver}"),
        };
    }

    public function vision(): Contracts\VisionInterface
    {
        $driver = config('ai.vision', 'openai');
        
        return match ($driver) {
            'openai' => new Providers\OpenAiVisionProvider(),
            default => throw new \Exception("Unknown vision driver: {$driver}"),
        };
    }

    public function generateText(string $prompt, array $options = [])
    {
        return $this->text()->generate($prompt, $options);
    }

    public function generateImage(string $prompt, array $options = [])
    {
        return $this->image()->generate($prompt, $options);
    }

    public function transcribe(\Illuminate\Http\UploadedFile $file)
    {
        return $this->transcriber()->transcribe($file);
    }

    public function speak(string $text, string $voiceId = 'oliver')
    {
        return $this->speech()->speak($text, $voiceId);
    }

    public function describeImage(\Illuminate\Http\UploadedFile $image)
    {
        return $this->vision()->describe($image);
    }

    public function embedding(): Contracts\EmbeddingInterface
    {
        $driver = config('ai.embedding', 'openai');
        
        return match ($driver) {
            'openai' => new Providers\OpenAiEmbeddingProvider(),
            'ollama' => new Providers\OllamaEmbeddingProvider(),
            default => throw new \Exception("Unknown embedding driver: {$driver}"),
        };
    }

    public function vectorStore(): Contracts\VectorStoreInterface
    {
        $driver = config('ai.vector_store', 'pinecone');
        
        return match ($driver) {
            'pinecone' => new Providers\PineconeVectorStoreProvider(),
            'json' => new Providers\JsonVectorStoreProvider(),
            default => throw new \Exception("Unknown vector store driver: {$driver}"),
        };
    }

    public function search(string $query, int $limit = 5)
    {
        $vector = $this->embedding()->embed($query);
        return $this->vectorStore()->search($vector, $limit);
    }
}
