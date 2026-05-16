<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categoryId = $request->get('category_id');

            $query = Product::select('id', 'code', 'name', 'category_id', 'min_price', 'is_active')
                ->with(['category:id,name'])
                ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId));

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('category_name', fn ($row) => $row->category?->name ?? '-')
                ->editColumn('min_price', fn ($row) => number_format($row->min_price, 0, ',', '.'))
                ->editColumn('is_active', fn ($row) => $row->is_active
                    ? '<span class="badge bg-success">' . __('common.active') . '</span>'
                    : '<span class="badge bg-secondary">' . __('common.inactive') . '</span>')
                ->addColumn('actions', function ($row) {
                    $edit   = '<a href="' . route('products.edit', $row->id) . '" class="btn btn-warning btn-sm">' . __('common.edit') . '</a>';
                    $delete = '<form method="POST" action="' . route('products.destroy', $row->id) . '" class="d-inline"
                                onsubmit="return confirm(\'' . addslashes(__('common.confirm_delete')) . '\')">'
                        . '<input type="hidden" name="_token" value="' . csrf_token() . '">'
                        . '<input type="hidden" name="_method" value="DELETE">'
                        . '<button type="submit" class="btn btn-danger btn-sm">' . __('common.delete') . '</button>'
                        . '</form>';
                    return $edit . ' ' . $delete;
                })
                ->rawColumns(['is_active', 'actions'])
                ->filterColumn('name', fn ($q, $keyword) => $q->where(
                    fn ($q) => $q->where('name', 'like', "%{$keyword}%")->orWhere('code', 'like', "%{$keyword}%")
                ))
                ->make(true);
        }

        $categories = ProductCategory::select('id', 'name')->orderBy('name')->get();

        return view('products.index', compact('categories'));
    }

    public function create()
    {
        $categories = ProductCategory::select('id', 'name')->orderBy('name')->get();
        $units      = ProductUnit::select('id', 'name')->orderBy('name')->get();
        return view('products.form', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'          => 'nullable|exists:product_categories,id',
            'code'                 => 'required|string|max:50|unique:products,code',
            'name'                 => 'required|string|max:150',
            'description'          => 'nullable|string',
            'min_price'            => 'required|numeric|min:0',
            'is_active'            => 'boolean',
            'unit_ids'             => 'nullable|array',
            'unit_ids.*'           => 'integer|exists:product_units,id',
            'variants'             => 'nullable|array',
            'variants.*.name'      => 'required|string|max:100',
            'variants.*.sku'       => 'nullable|string|max:100',
            'variants.*.prices'    => 'nullable|array',
            'variants.*.prices.*'  => 'nullable|numeric|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($validated);

        $unitIds = $request->input('unit_ids', []);
        $product->units()->sync($unitIds);

        foreach ($request->input('variants', []) as $v) {
            if (empty($v['name'])) continue;
            $variant = $product->variants()->create([
                'name'      => $v['name'],
                'sku'       => $v['sku'] ?: null,
                'is_active' => (bool) ($v['is_active'] ?? false),
            ]);
            foreach ($unitIds as $unitId) {
                $variant->prices()->create([
                    'product_unit_id' => $unitId,
                    'price'           => $v['prices'][$unitId] ?? 0,
                ]);
            }
        }

        return redirect()->route('products.index')
            ->with('success', __('common.success_create', ['name' => __('product.product')]));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::select('id', 'name')->orderBy('name')->get();
        $units      = ProductUnit::select('id', 'name')->orderBy('name')->get();
        $product->load([
            'units:id,name',
            'variants' => fn ($q) => $q->select('id', 'product_id', 'name', 'sku', 'is_active'),
            'variants.prices',
        ]);
        return view('products.form', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'          => 'nullable|exists:product_categories,id',
            'code'                 => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product->id)],
            'name'                 => 'required|string|max:150',
            'description'          => 'nullable|string',
            'min_price'            => 'required|numeric|min:0',
            'is_active'            => 'boolean',
            'unit_ids'             => 'nullable|array',
            'unit_ids.*'           => 'integer|exists:product_units,id',
            'variants'             => 'nullable|array',
            'variants.*.name'      => 'required|string|max:100',
            'variants.*.sku'       => 'nullable|string|max:100',
            'variants.*.prices'    => 'nullable|array',
            'variants.*.prices.*'  => 'nullable|numeric|min:0',
            'deleted_variant_ids'   => 'nullable|array',
            'deleted_variant_ids.*' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $product->update($validated);

        $unitIds = $request->input('unit_ids', []);
        $product->units()->sync($unitIds);

        // Delete removed variants (cascades to their prices)
        $deletedIds = array_filter((array) $request->input('deleted_variant_ids', []));
        if ($deletedIds) {
            ProductVariant::whereIn('id', $deletedIds)
                ->where('product_id', $product->id)
                ->delete();
        }

        // Remove prices for units no longer attached to this product
        if ($unitIds) {
            ProductVariantPrice::whereHas('variant', fn ($q) => $q->where('product_id', $product->id))
                ->whereNotIn('product_unit_id', $unitIds)
                ->delete();
        } else {
            ProductVariantPrice::whereHas('variant', fn ($q) => $q->where('product_id', $product->id))
                ->delete();
        }

        // Update existing or create new variants, then upsert prices
        foreach ($request->input('variants', []) as $v) {
            if (empty($v['name'])) continue;
            $data = [
                'name'      => $v['name'],
                'sku'       => $v['sku'] ?: null,
                'is_active' => (bool) ($v['is_active'] ?? false),
            ];
            if (!empty($v['id'])) {
                ProductVariant::where('id', $v['id'])
                    ->where('product_id', $product->id)
                    ->update($data);
                $variantId = (int) $v['id'];
            } else {
                $variant   = $product->variants()->create($data);
                $variantId = $variant->id;
            }

            foreach ($unitIds as $unitId) {
                ProductVariantPrice::updateOrCreate(
                    ['product_variant_id' => $variantId, 'product_unit_id' => $unitId],
                    ['price' => $v['prices'][$unitId] ?? 0]
                );
            }
        }

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
