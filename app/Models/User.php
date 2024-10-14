<?php

namespace App\Models;

use App\Enums\WishListEnum;
use App\Notifications\WishList\ProductInStockNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'phone',
        'email',
        'birthdate',
        'created_at',
        'updated_at',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function wishes(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'wish_list',
            'user_id',
            'product_id'
        )->withPivot(['price', 'in_stock']);
    }

    public function addToWish(Product $product, WishListEnum $type = WishListEnum::Price): void
    {
        $wished = $this->wishes()->find($product);

        $data = [
            $type->value => true,
        ];

        if ($wished) {
            $this->wishes()->updateExistingPivot($wished, $data);
        } else {
            $this->wishes()->attach($product, $data);
        }
    }

    public function removeFromWish(Product $product, WishListEnum $type = WishListEnum::Price): void
    {
        $this->wishes()->updateExistingPivot($product, [$type->value => false]);

        $product = $this->wishes()->find($product);

        if (!$product->pivot->in_stock && !$product->pivot->price) {
            $this->wishes()->detach($product);
        }
    }

    public function isWishedProduct(Product $product, WishListEnum $type = WishListEnum::Price): bool
    {
        return $this->wishes()
            ->where('product_id', $product->id)
            ->wherePivot($type->value, true)
            ->exists();
    }
}
