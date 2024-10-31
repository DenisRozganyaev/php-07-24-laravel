<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService implements Contracts\FileServiceContract
{
    public function upload(UploadedFile|string $file, string $additionalPath = ''): string
    {
        if (is_string($file)) {
            return $file;
        }

        $additionalPath = ! empty($additionalPath) ? $additionalPath.'/' : '';

        $filePath = Str::slug(microtime());
        $filePath = $additionalPath.$filePath.'_'.$file->getClientOriginalName();
        Storage::put($filePath, File::get($file));
        Storage::setVisibility($filePath, 'public');

        return $filePath;
    }

    public function delete(string $filePath): void
    {
        Storage::delete($filePath);

        $path = collect(explode('/', $filePath));
        $path = $path->except($path->keys()->last())->implode('/');

        if (empty(Storage::files($path))) {
            Storage::deleteDirectory($path);
        }
    }
}
