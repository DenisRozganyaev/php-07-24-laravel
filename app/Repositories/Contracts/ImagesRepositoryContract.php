<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\Admin\Products\CreateRequest;
use App\Http\Requests\Admin\Products\EditRequest;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

interface ImagesRepositoryContract
{
    public function attach(Model $model, string $relation, array $images = [], string $dir = ''): void;
}
