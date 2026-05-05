<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->get('search');
        $categoryId = $request->get('category_id');

        $products = Product::with(['category', 'unit'])
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = ProductCategory::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        $units      = ProductUnit::orderBy('name')->get();
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_id'     => 'nullable|exists:product_units,id',
            'code'        => 'required|string|max:50|unique:products,code',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'base_price'  => 'required|numeric|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', __('common.success_create', ['name' => __('product.product')]));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        $units      = ProductUnit::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_id'     => 'nullable|exists:product_units,id',
            'code'        => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product->id)],
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'base_price'  => 'required|numeric|min:0',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', __('common.success_update', ['name' => __('product.product')]));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', __('common.success_delete', ['name' => __('product.product')]));
    }
}
