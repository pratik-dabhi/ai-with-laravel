<?php

namespace App\Services\AI\Contracts;

use Illuminate\Http\UploadedFile;

interface TranscriberInterface
{
    public function transcribe(UploadedFile $file, string $model = 'nova-3');
}
