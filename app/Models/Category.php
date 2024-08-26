<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    // $category->products => Collection of all products
    // $category->products() => query builder for products (for this category)
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
