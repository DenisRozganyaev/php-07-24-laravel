<?php

namespace App\Models;

use App\Observers\ImageObserver;
use App\Services\Contracts\FileServiceContract;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin IdeHelperImage
 */
#[ObservedBy([ImageObserver::class])]
class Image extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function setPathAttribute(array $path): void
    {
        $this->attributes['path'] = app(FileServiceContract::class)->upload(
            $path['image'],
            $path['dir'] ?? null
        );
    }

    public function url(): Attribute
    {
        return Attribute::get(function () {
            if (Storage::disk('public')->exists($this->attributes['path'])) {
                return Storage::disk('public')->url($this->attributes['path']);
            }

            if (Storage::exists($this->attributes['path'])) {
                $key = 'products.images.' . $this->attributes['path'];

                if (!cache()->has($key)) {
                    $temporaryUrl = Storage::temporaryUrl(
                        $this->attributes['path'],
                        now()->addMinutes(10)
                    );

                    cache()->put($key, $temporaryUrl, 590);
                }

                return cache()->get($key);
            }

            return $this->attributes['path'];
        });
    }
}
