<?php

namespace App\Services;

use App\Services\Contracts\FileServiceContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileService implements Contracts\FileServiceContract
{

    public function upload(UploadedFile $file, string $additionalPath = ''): string
    {
        $additionalPath = !empty($additionalPath) ? $additionalPath . '/' : '';

        $filePath = $additionalPath . time() . '_' . $file->getClientOriginalName();
        Storage::put($filePath, File::get($file));

        return $filePath;
    }

    public function delete(string $filePath): void
    {
        Storage::delete($filePath);
    }
}
