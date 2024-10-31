<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Permission\CategoryEnum as Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Categories\EditRequest;
use App\Http\Requests\Attributes\CreateRequest;
use App\Models\Attributes\Attribute;
use App\Models\Category;
use Illuminate\Support\Str;

class AttributesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attributes = Attribute::with(['options'])->paginate(15);

        return view('admin/attributes/index', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin/attributes/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $name = $request->get('name');
        $slug = Str::slug($name);

        $attribute = Attribute::create(compact('name', 'slug'));

        $attribute->options()->createMany($request->get('options'));

        return redirect()->route('admin.attributes.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin/categories/edit', [
            'category' => $category,
            'list' => Category::select(['id', 'name'])
                ->whereNot('id', $category->id)
                ->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditRequest $request, Category $category)
    {
        $data = array_merge(
            $request->validated(),
            ['slug' => Str::slug($request->get('name'))]
        );

        $category->updateOrFail($data);

        return redirect()->route('admin.categories.edit', $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->middleware('permission:'.Permission::DELETE->value);

        $category->deleteOrFail();

        return redirect()->route('admin.categories.index');
    }
}
