<?php

namespace App\Repositories;

use App\Http\Requests\Admin\Products\CreateRequest;
use App\Http\Requests\Admin\Products\EditRequest;
use App\Models\Product;
use App\Repositories\Contracts\ImagesRepositoryContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsRepository implements Contracts\ProductsRepositoryContract
{
    public function __construct(protected ImagesRepositoryContract $imagesRepository)
    {
    }

    public function paginate(Request $request): LengthAwarePaginator
    {
        $products = Product::with(['categories'])
            ->select('products.*')
            ->orderByDesc('id')
            ->when($request->has('options'), function(Builder $query) use ($request) {
                $query->join('attribute_option_product', 'products.id', '=', 'attribute_option_product.product_id')
                    ->where('attribute_option_product.attribute_option_id', $request->input('options'))
                    ->distinct();
            });

        return $products->paginate($request->input('per_page', 10));
    }

    public function store(CreateRequest $request): Product|false
    {
        try {
            DB::beginTransaction();

            $data = $this->formRequestData($request);
            $product = Product::create($data['attributes']);
            $this->updateRelationData($product, $data);

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
        try {
            DB::beginTransaction();

            $data = $this->formRequestData($request);

            $product->update($data['attributes']);
            $this->updateRelationData($product, $data);

            DB::commit();

            return true;
        } catch (\Throwable $exception) {
            DB::rollBack();
            logs()->error($exception);
            return false;
        }
    }

    protected function updateRelationData(Product $product, array $data): void
    {
        $product->categories()->sync($data['categories'] ?? []);

        $this->imagesRepository->attach(
            $product,
            'images',
            $data['images'] ?? [],
            $product->imagesPath
        );

        if(!empty($data['options'])) {
            $options = collect($data['options'])
                ->unique(fn ($item) => $item['attribute_option_id'])
                ->values()
                ->toArray();

            $product->options()->detach();

            $product->options()->sync($options);
        }
    }

    protected function formRequestData(CreateRequest|EditRequest $request): array
    {
        return [
            'attributes' => collect($request->validated())
                ->except(['categories', 'images', 'options'])
                ->prepend(Str::slug($request->get('title')), 'slug')
                ->toArray(),
            'categories' => $request->get('categories', []),
            'images' => $request->file('images', []),
            'options' => $request->get('options', []),
        ];
    }
}
