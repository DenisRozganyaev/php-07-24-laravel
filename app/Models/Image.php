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
        return Attribute::get(function() {
            return Storage::url($this->attributes['path']);
        });
    }
}
