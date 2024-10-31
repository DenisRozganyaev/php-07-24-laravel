<?php

namespace App\Models\Attributes;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\JoinClause;

class Option extends Model
{
    use HasFactory;

    protected $table = 'attribute_options';

    protected $guarded = [];

    public function scopeFilter(Builder $query, array $ids = []): Collection
    {
        $productsIds = Product::when($ids, function (Builder $query) use ($ids) {
            $query->whereIn('id', $ids);
        })->get()->pluck('id');

        $attributes = $query->join('attribute_option_product', function (JoinClause $join) use ($productsIds) {
            $join->on('attribute_option_product.attribute_option_id', '=', 'attribute_options.id')
                ->whereIn('attribute_option_product.product_id', $productsIds);
        })
            ->with(['attribute'])
            ->withCount(['products'])
            ->distinct()
            ->get();

        return $attributes->isEmpty()
            ? $attributes
            : $attributes?->groupBy(fn ($item) => $item->attribute->name);
    }

    // attribute_id
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'attribute_option_product', 'attribute_option_id', 'product_id')
            ->withPivot(['quantity', 'price']);
    }
}
