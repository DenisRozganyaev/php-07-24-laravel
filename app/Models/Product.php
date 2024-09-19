<?php

namespace App\Models;

use App\Models\Attributes\Option;
use App\Observers\ProductObserver;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperProduct
 */
#[ObservedBy(ProductObserver::class)]
class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'attribute_option_product', 'product_id', 'attribute_option_id')
            ->withPivot(['quantity', 'price']);
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::get(function() {
            return Storage::url($this->attributes['thumbnail']);
        });
    }

    public function setThumbnailAttribute($image) {
        $fileService = app(FileServiceContract::class);

        if (! empty($this->attributes['thumbnail'])) {
            $fileService->delete($this->attributes['thumbnail']);
        }

        $this->attributes['thumbnail'] = $fileService->upload(
            $image,
            'products/' . $this->attributes['slug']
        );
    }

    public function imagesPath(): Attribute
    {
        return Attribute::get(fn() => 'products/' . $this->attributes['slug']);
    }

    public function withDiscount(): Attribute
    {
        return Attribute::get(fn() => $this->attributes['discount'] > 0);
    }

    public function finalPrice(): Attribute
    {
        return Attribute::get(fn() => round(
            $this->attributes['price'] - ($this->attributes['price'] * ($this->attributes['discount'] / 100)),
            2
        ));
    }
}
