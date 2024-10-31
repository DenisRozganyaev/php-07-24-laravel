<?php

namespace Tests\Unit\Services;

use App\Services\Contracts\FileServiceContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    const FILE_NAME = 'image.png';

    protected FileServiceContract $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FileServiceContract::class);
        Storage::fake('public');
    }

    public function test_it_uploads_valid_file(): void
    {
        $uploadedFile = $this->uploadFile();

        $this->assertTrue(Storage::has($uploadedFile));
        $this->assertEquals(Storage::getVisibility($uploadedFile), 'public');
        $this->assertNotEquals($uploadedFile, self::FILE_NAME);
    }

    public function test_it_uploads_valid_file_with_additional_path(): void
    {
        $uploadedFile = $this->uploadFile(additionalPath: 'products/test');

        $this->assertTrue(Storage::has($uploadedFile));
        $this->assertEquals(Storage::getVisibility($uploadedFile), 'public');
        $this->assertStringContainsString('products/test', $uploadedFile);
    }

    public function test_it_uploads_two_files_with_the_same_name(): void
    {
        $this->uploadFile(additionalPath: 'products/test');
        $this->uploadFile(additionalPath: 'products/test');

        $files = Storage::allFiles('products/test');
        $this->assertCount(2, $files);
    }

    public function test_it_removes_file(): void
    {
        $path = $this->uploadFile(additionalPath: 'products/test');

        $this->assertTrue(Storage::has($path));

        $this->service->delete($path);

        $this->assertFalse(Storage::has($path));
    }

    public function test_it_removes_directory_if_it_empty(): void
    {
        $dir = 'products/test';
        $path = $this->uploadFile(additionalPath: 'products/test');

        $this->assertTrue(Storage::has($dir));

        $this->service->delete($path);

        $this->assertFalse(Storage::has($dir));
    }

    protected function uploadFile(?string $fileName = null, mixed $additionalPath = ''): string
    {
        $file = UploadedFile::fake()->image($fileName ?? self::FILE_NAME);

        return $this->service->upload($file, $additionalPath);
    }
}
