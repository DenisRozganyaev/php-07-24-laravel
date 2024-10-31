<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Permission\CategoryEnum as Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Categories\CreateRequest;
use App\Http\Requests\Admin\Categories\EditRequest;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with(['parent'])->paginate(15);

        return view('admin/categories/index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::select(['id', 'name'])->get();

        return view('admin/categories/create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = array_merge(
            $request->validated(),
            ['slug' => Str::slug($request->get('name'))]
        );

        $category = Category::create($data);

        notify()->success("Category [$category->name] was created");

        return redirect()->route('admin.categories.index');
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
