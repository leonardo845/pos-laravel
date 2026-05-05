<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $categories = ProductCategory::withCount('products')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('product-categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('product-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:product_categories,name',
        ]);

        ProductCategory::create($validated);

        return redirect()->route('product-categories.index')
            ->with('success', __('common.success_create', ['name' => __('product.product_category')]));
    }

    public function edit(ProductCategory $productCategory)
    {
        return view('product-categories.edit', compact('productCategory'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100',
                Rule::unique('product_categories', 'name')->ignore($productCategory->id)],
        ]);

        $productCategory->update($validated);

        return redirect()->route('product-categories.index')
            ->with('success', __('common.success_update', ['name' => __('product.product_category')]));
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        return redirect()->route('product-categories.index')
            ->with('success', __('common.success_delete', ['name' => __('product.product_category')]));
    }
}
