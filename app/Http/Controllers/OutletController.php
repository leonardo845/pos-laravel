<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\OutletProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OutletController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $outlets = Outlet::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('outlets.index', compact('outlets', 'search'));
    }

    public function create()
    {
        return view('outlets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'address'   => 'nullable|string',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Outlet::create($validated);

        return redirect()->route('outlets.index')
            ->with('success', __('common.success_create', ['name' => __('outlet.outlet')]));
    }

    public function edit(Outlet $outlet)
    {
        return view('outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'address'   => 'nullable|string',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $outlet->update($validated);

        return redirect()->route('outlets.index')
            ->with('success', __('common.success_update', ['name' => __('outlet.outlet')]));
    }

    public function destroy(Outlet $outlet)
    {
        $outlet->delete();

        return redirect()->route('outlets.index')
            ->with('success', __('common.success_delete', ['name' => __('outlet.outlet')]));
    }

    public function products(Outlet $outlet)
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $assignedIds = OutletProduct::withTrashed(false)
            ->where('outlet_id', $outlet->id)
            ->pluck('is_active', 'product_id');

        return view('outlets.products', compact('outlet', 'products', 'assignedIds'));
    }

    public function syncProducts(Request $request, Outlet $outlet)
    {
        $request->validate([
            'products'   => 'nullable|array',
            'products.*' => 'exists:products,id',
        ]);

        $selectedIds = $request->input('products', []);

        // Update or create each product entry
        $allProductIds = Product::pluck('id');
        foreach ($allProductIds as $productId) {
            $isActive = in_array($productId, $selectedIds);
            OutletProduct::updateOrCreate(
                ['outlet_id' => $outlet->id, 'product_id' => $productId],
                ['is_active' => $isActive]
            );
        }

        return redirect()->route('outlets.products', $outlet)
            ->with('success', __('common.success_sync'));
    }
}
