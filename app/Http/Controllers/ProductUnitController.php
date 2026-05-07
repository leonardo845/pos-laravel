<?php

namespace App\Http\Controllers;

use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductUnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $units = ProductUnit::select('id', 'name')
            ->withCount('products')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('product-units.index', compact('units', 'search'));
    }

    public function create()
    {
        return view('product-units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:product_units,name',
        ]);

        ProductUnit::create($validated);

        return redirect()->route('product-units.index')
            ->with('success', __('common.success_create', ['name' => __('product.product_unit')]));
    }

    public function edit(ProductUnit $productUnit)
    {
        return view('product-units.edit', compact('productUnit'));
    }

    public function update(Request $request, ProductUnit $productUnit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100',
                Rule::unique('product_units', 'name')->ignore($productUnit->id)],
        ]);

        $productUnit->update($validated);

        return redirect()->route('product-units.index')
            ->with('success', __('common.success_update', ['name' => __('product.product_unit')]));
    }

    public function destroy(ProductUnit $productUnit)
    {
        $productUnit->delete();

        return redirect()->route('product-units.index')
            ->with('success', __('common.success_delete', ['name' => __('product.product_unit')]));
    }
}
