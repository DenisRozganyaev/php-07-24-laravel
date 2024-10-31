<?php

namespace App\Repositories\Contracts;

use App\Http\Requests\Admin\Products\CreateRequest;
use App\Http\Requests\Admin\Products\EditRequest;
use App\Http\Requests\Api\v1\StoreProductRequest;
use App\Http\Requests\Api\v1\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductsRepositoryContract
{
    public function paginate(Request $request): LengthAwarePaginator;

    public function store(CreateRequest|StoreProductRequest $request): Product|false;

    public function update(Product $product, EditRequest|UpdateProductRequest $request): bool;
}
