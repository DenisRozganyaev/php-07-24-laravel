<?php

namespace App\Models\Attributes;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Option extends Model
{
    use HasFactory;

    protected $table = 'attribute_options';

    protected $guarded = [];

    // attribute_id
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'attribute_option_product', 'attribute_option_id', 'product_id')
            ->withPivot(['quantity', 'price']);
    }
}
