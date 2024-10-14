<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Product;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\NoReturn;
use Mockery\MockInterface;
use Tests\Feature\Traits\SetupTrait;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
    use SetupTrait;

    #[NoReturn] public function test_it_creates_product_with_valid_data(): void
    {
        Storage::fake('public');

        $fileName = 'product.jpg';
        $file = UploadedFile::fake()->image($fileName);
        $productAttrs = [
            ...Product::factory()->make(['thumbnail' => ''])->toArray(),
            'thumbnail' => $file,
        ];

        $slug = $productAttrs['slug'];
        $imagePath = "$slug/$fileName";

        $this->mock(
            FileServiceContract::class,
            function (MockInterface $mock) use ($imagePath) {
                $mock->shouldReceive('upload')
                    ->andReturn($imagePath);
            }
        );

        $this->actingAs($this->user())
            ->post(route('admin.products.store'), $productAttrs);

        $this->assertDatabaseHas(Product::class, [
            'slug' => $slug,
            'thumbnail' => $imagePath,
        ]);
    }
}
