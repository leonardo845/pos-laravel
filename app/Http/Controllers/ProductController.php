<?php

namespace App\Http\Controllers;

use App\Models\Modifier;
use App\Models\ModifierGroup;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\ProductVariant;
use App\Models\ProductVariantModifier;
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
        $categories     = ProductCategory::select('id', 'name')->orderBy('name')->get();
        $units          = Unit::select('id', 'name')->orderBy('name')->get();
        $modifierGroups = ModifierGroup::with('modifiers')->orderBy('name')->get();
        return view('products.form', compact('categories', 'units', 'modifierGroups'));
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
            'unit_ids.*'           => 'integer|exists:units,id',
            'variants'             => 'nullable|array',
            'variants.*.name'      => 'required|string|max:100',
            'variants.*.sku'       => 'nullable|string|max:100',
            'variants.*.barcode'   => 'nullable|string|max:100',
            'variants.*.stock'     => 'nullable|numeric|min:0',
            'variants.*.prices'    => 'nullable|array',
            'variants.*.prices.*'  => 'nullable|numeric|min:0',
            'modifier_group_ids'   => 'nullable|array',
            'modifier_group_ids.*' => 'integer|exists:modifier_groups,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($validated);

        $unitIds = $request->input('unit_ids', []);
        $product->units()->sync($unitIds);
        $allModifiers     = Modifier::select('id', 'modifier_group_id')->get()->keyBy('id');
        $modifierGroupIds = [];

        foreach ($request->input('variants', []) as $v) {
            if (empty($v['name'])) continue;
            $variant = $product->variants()->create([
                'name'             => $v['name'],
                'sku'              => $v['sku'] ?: null,
                'barcode'          => !empty($v['barcode_auto']) ? $this->generateBarcode() : ($v['barcode'] ?: null),
                'stock'            => $v['stock'] ?? 0,
                'is_stock_tracked' => (bool) ($v['is_stock_tracked'] ?? false),
                'attributes'       => !empty($v['attributes']) ? json_decode($v['attributes'], true) : null,
                'is_active'        => (bool) ($v['is_active'] ?? false),
            ]);
            foreach ($unitIds as $unitId) {
                $variant->prices()->create([
                    'unit_id' => $unitId,
                    'price'   => $v['prices'][$unitId] ?? 0,
                ]);
            }
            foreach (array_keys($v['modifier_enabled'] ?? []) as $modifierId) {
                $variant->variantModifiers()->create([
                    'modifier_id' => $modifierId,
                    'price'       => $v['modifier_prices'][$modifierId] ?? 0,
                ]);
                if (isset($allModifiers[$modifierId])) {
                    $modifierGroupIds[] = $allModifiers[$modifierId]->modifier_group_id;
                }
            }
        }
        $explicitGroupIds = array_map('intval', $request->input('modifier_group_ids', []));
        $product->modifierGroups()->sync(array_unique(array_merge($explicitGroupIds, $modifierGroupIds)));

        return redirect()->route('products.index')
            ->with('success', __('common.success_create', ['name' => __('product.product')]));
    }

    public function edit(Product $product)
    {
        $categories     = ProductCategory::select('id', 'name')->orderBy('name')->get();
        $units          = Unit::select('id', 'name')->orderBy('name')->get();
        $modifierGroups = ModifierGroup::with('modifiers')->orderBy('name')->get();
        $product->load([
            'units:id,name',
            'modifierGroups:id,name',
            'variants' => fn ($q) => $q->select('id', 'product_id', 'name', 'sku', 'barcode', 'is_stock_tracked', 'attributes', 'is_active'),
            'variants.prices',
            'variants.variantModifiers',
        ]);
        return view('products.form', compact('product', 'categories', 'units', 'modifierGroups'));
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
            'unit_ids.*'           => 'integer|exists:units,id',
            'variants'             => 'nullable|array',
            'variants.*.name'      => 'required|string|max:100',
            'variants.*.sku'       => 'nullable|string|max:100',
            'variants.*.barcode'   => 'nullable|string|max:100',
            'variants.*.stock'     => 'nullable|numeric|min:0',
            'variants.*.prices'    => 'nullable|array',
            'variants.*.prices.*'  => 'nullable|numeric|min:0',
            'modifier_group_ids'    => 'nullable|array',
            'modifier_group_ids.*'  => 'integer|exists:modifier_groups,id',
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
                ->whereNotIn('unit_id', $unitIds)
                ->delete();
        } else {
            ProductVariantPrice::whereHas('variant', fn ($q) => $q->where('product_id', $product->id))
                ->delete();
        }

        // Update existing or create new variants, then upsert prices
        $allModifiers     = Modifier::select('id', 'modifier_group_id')->get()->keyBy('id');
        $modifierGroupIds = [];
        foreach ($request->input('variants', []) as $v) {
            if (empty($v['name'])) continue;
            $data = [
                'name'             => $v['name'],
                'sku'              => $v['sku'] ?: null,
                'barcode'          => !empty($v['barcode_auto']) ? $this->generateBarcode() : ($v['barcode'] ?: null),
                'stock'            => $v['stock'] ?? 0,
                'is_stock_tracked' => (bool) ($v['is_stock_tracked'] ?? false),
                'attributes'       => !empty($v['attributes']) ? json_decode($v['attributes'], true) : null,
                'is_active'        => (bool) ($v['is_active'] ?? false),
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
                    ['product_variant_id' => $variantId, 'unit_id' => $unitId],
                    ['price' => $v['prices'][$unitId] ?? 0]
                );
            }

            // Sync variant modifiers
            ProductVariantModifier::where('product_variant_id', $variantId)->delete();
            foreach (array_keys($v['modifier_enabled'] ?? []) as $modifierId) {
                ProductVariantModifier::create([
                    'product_variant_id' => $variantId,
                    'modifier_id'        => $modifierId,
                    'price'              => $v['modifier_prices'][$modifierId] ?? 0,
                ]);
                if (isset($allModifiers[$modifierId])) {
                    $modifierGroupIds[] = $allModifiers[$modifierId]->modifier_group_id;
                }
            }
        }
        $explicitGroupIds = array_map('intval', $request->input('modifier_group_ids', []));
        $product->modifierGroups()->sync(array_unique(array_merge($explicitGroupIds, $modifierGroupIds)));

        return redirect()->route('products.index')
            ->with('success', __('common.success_update', ['name' => __('product.product')]));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', __('common.success_delete', ['name' => __('product.product')]));
    }

    private function generateBarcode(): string
    {
        do {
            $partial = '200' . str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
            $barcode = $partial . $this->ean13CheckDigit($partial);
        } while (ProductVariant::where('barcode', $barcode)->exists());

        return $barcode;
    }

    private function ean13CheckDigit(string $digits): int
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $digits[$i] * ($i % 2 === 0 ? 1 : 3);
        }
        return (10 - ($sum % 10)) % 10;
    }
}
