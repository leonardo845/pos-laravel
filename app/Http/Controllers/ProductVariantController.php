<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->get('search');
        $productId = $request->get('product_id');

        $variants = ProductVariant::with('product')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%"))
            ->when($productId, fn ($q) => $q->where('product_id', $productId))
            ->orderBy('product_id')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $products = Product::orderBy('name')->get();

        return view('product-variants.index', compact('variants', 'products', 'search', 'productId'));
    }

    public function create(Request $request)
    {
        $products   = Product::orderBy('name')->get();
        $productId  = $request->get('product_id');
        return view('product-variants.create', compact('products', 'productId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name'       => 'required|string|max:100',
            'sku'        => 'nullable|string|max:100|unique:product_variants,sku',
            'price'      => 'nullable|numeric|min:0',
            'stock'      => 'required|integer|min:0',
            'is_active'  => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        ProductVariant::create($validated);

        return redirect()->route('product-variants.index')
            ->with('success', __('common.success_create', ['name' => __('product.product_variant')]));
    }

    public function edit(ProductVariant $productVariant)
    {
        $products = Product::orderBy('name')->get();
        return view('product-variants.edit', compact('productVariant', 'products'));
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name'       => 'required|string|max:100',
            'sku'        => ['nullable', 'string', 'max:100',
                Rule::unique('product_variants', 'sku')->ignore($productVariant->id)],
            'price'      => 'nullable|numeric|min:0',
            'stock'      => 'required|integer|min:0',
            'is_active'  => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $productVariant->update($validated);

        return redirect()->route('product-variants.index')
            ->with('success', __('common.success_update', ['name' => __('product.product_variant')]));
    }

    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return redirect()->route('product-variants.index')
            ->with('success', __('common.success_delete', ['name' => __('product.product_variant')]));
    }
}
