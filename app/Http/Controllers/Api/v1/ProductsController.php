<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreProductRequest;
use App\Http\Requests\Api\v1\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\Contracts\ProductsRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ProductsRepositoryContract $repository)
    {
        return ProductResource::collection($repository->paginate($request));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, ProductsRepositoryContract $repository)
    {
        if ($product = $repository->store($request)) {
            return response()->json([
                'status' => 'success',
                'data' => new ProductResource($product)
            ]);
        }

        return response()->json([
            'status' => 'error',
            'data' => [
                'message' => 'Something went wrong'
            ]
        ], 422);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->loadMissing(['categories', 'images']);

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product, ProductsRepositoryContract $repository)
    {
        if ($repository->update($product, $request)) {
            return response()->json([
                'status' => 'success',
                'data' => new ProductResource($product)
            ]);
        }

        return response()->json([
            'status' => 'error',
            'data' => [
                'message' => 'Something went wrong'
            ]
        ], 422);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            $product->images()->delete();
            $product->deleteOrFail();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => new ProductResource($product)
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            logs()->error($exception->getMessage());

            return response()->json([
                'status' => 'error',
                'data' => [
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode()
                ]
            ], 422);
        }
    }
}
