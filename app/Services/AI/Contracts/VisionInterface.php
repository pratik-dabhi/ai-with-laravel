<?php

namespace App\Services\AI\Contracts;

use Illuminate\Http\UploadedFile;

interface VisionInterface
{
    public function describe(UploadedFile $image): string;
}
