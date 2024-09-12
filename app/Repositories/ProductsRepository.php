<?php

namespace App\Repositories;

use App\Http\Requests\Admin\Products\CreateRequest;
use App\Http\Requests\Admin\Products\EditRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsRepository implements Contracts\ProductsRepositoryContract
{
    public function store(CreateRequest $request): Product|false
    {
        try {
            DB::beginTransaction();

            $data = $this->formRequestData($request);
            $product = Product::create($data['attributes']);
            $product->categories()->sync($data['categories']);

            DB::commit();

            return $product;
        } catch (\Throwable $exception) {
            DB::rollBack();
            logs()->error($exception);
            return false;
        }
    }

    public function update(Product $product, EditRequest $request): bool
    {
        return false;
    }

    protected function formRequestData(CreateRequest|EditRequest $request): array
    {
        return [
            'attributes' => collect($request->validated())
                ->except(['categories'])
                ->prepend(Str::slug($request->get('title')), 'slug')
                ->toArray(),
            'categories' => $request->get('categories', [])
        ];
    }
}
