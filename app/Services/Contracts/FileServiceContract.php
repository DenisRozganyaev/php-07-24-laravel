<?php

namespace App\Services\Contracts;

use Illuminate\Http\UploadedFile;

interface FileServiceContract
{
    public function upload(UploadedFile|string $file, string $additionalPath = ''): string;

    public function delete(string $filePath): void;
}
