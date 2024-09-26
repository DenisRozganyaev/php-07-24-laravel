<?php

namespace App\Models;

use App\Models\Attributes\Option;
use App\Observers\ProductObserver;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
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

    protected $appends = [
        'option_price' => null
    ];

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
            ->withPivot(['quantity', 'price'])
            ->with(['attribute']);
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

    public function isSimple(): Attribute
    {
        return Attribute::get(fn() => $this->options->isEmpty());
    }

    public function optionsWithAttributes(): Collection
    {
        return $this->options()
            ->with(['attribute'])
            ->get()
            ?->groupBy(fn($item) => $item->attribute->name);
    }

    public function finalPrice($price = null): float
    {
        $price = $price ?? $this->attributes['price'];

        return round(
            $price - ($price * ($this->attributes['discount'] / 100)),
            2
        );
    }
}
