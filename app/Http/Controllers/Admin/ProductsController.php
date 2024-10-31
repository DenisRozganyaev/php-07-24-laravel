<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Permission\ProductEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\CreateRequest;
use App\Http\Requests\Admin\Products\EditRequest;
use App\Models\Attributes\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductsRepositoryContract;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['categories'])->orderByDesc('id')->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create', ['categories' => Category::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request, ProductsRepositoryContract $repository)
    {
        if ($product = $repository->store($request)) {
            notify()->success("Product '$product->title' was created");

            return redirect()->route('admin.products.index');
        }

        notify()->error('Oops! Something went wrong.');

        return redirect()->back()->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['categories', 'images', 'options']);

        $selectedOptions = $product->options->pluck('id')->toArray();
        $productCategories = $product->categories->pluck('id')->toArray();
        $attributes = Attribute::with('options')->get();

        return view('admin.products.edit', [
            'categories' => Category::all(),
            'product' => $product,
            'productCategories' => $productCategories,
            'attributes' => $attributes,
            'selectedOptions' => $selectedOptions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditRequest $request, Product $product, ProductsRepositoryContract $repository)
    {
        if ($repository->update($product, $request)) {
            notify()->success("Product '$product->title' was updated");

            return redirect()->route('admin.products.edit', $product);
        }

        notify()->error('Oops! Something went wrong.');

        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $this->middleware('permission:'.ProductEnum::DELETE->value);
            $product->deleteOrFail();

            notify()->success("Product '$product->title' was updated");

            return redirect()->back();
        } catch (\Throwable $exception) {
            logs()->error($exception->getMessage());
            notify()->error('Oops! Something went wrong.');

            return redirect()->back()->withInput();
        }
    }
}
