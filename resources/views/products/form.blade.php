@extends('layouts.app')

@php
    $isEdit = isset($product);
    $oldVariants    = old('variants');
    $useOldVariants = $oldVariants !== null;

    $renderedCount = $useOldVariants
        ? count($oldVariants)
        : ($isEdit ? $product->variants->count() : 0);

    $defaultUnitIds   = $isEdit ? $product->units->pluck('id')->toArray() : [];
    $selectedUnitObjs = ($isEdit && !$useOldVariants)
        ? $product->units
        : $units->whereIn('id', array_map('intval', (array) old('unit_ids', $defaultUnitIds)));

    $unitRowCount = $selectedUnitObjs->count();

    // Selected modifier groups for "Opsi Tambahan" section
    if ($useOldVariants) {
        $selectedModifierGroupIds = array_map('intval', old('modifier_group_ids', []));
    } elseif ($isEdit) {
        $selectedModifierGroupIds = $product->modifierGroups->pluck('id')->toArray();
    } else {
        $selectedModifierGroupIds = [];
    }
    $selectedModifierGroupObjs = $modifierGroups->whereIn('id', $selectedModifierGroupIds)->values();
@endphp

@section('title', ($isEdit ? __('common.edit') : __('common.add')) . ' ' . __('product.product'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $isEdit ? __('common.edit') : __('common.add') }} {{ __('product.product') }}</h4>
    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('products.update', $product) : route('products.store') }}">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code" class="form-label">{{ __('common.code') }}</label>
                        <input type="text" name="code" id="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $isEdit ? $product->code : '') }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('common.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $isEdit ? $product->name : '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">{{ __('product.category') }} <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- {{ __('product.category') }} --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $isEdit ? $product->category_id : '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>


                </div><!-- /col-md-6 -->

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('product.min_price') }}</label>
                        <input type="number" id="min_price_display" min="0" step="0.01"
                               class="form-control @error('min_price') is-invalid @enderror bg-light"
                               value="{{ old('min_price', $isEdit ? $product->min_price : 0) }}" disabled>
                        <input type="hidden" name="min_price" id="min_price_hidden"
                               value="{{ old('min_price', $isEdit ? $product->min_price : 0) }}">
                        @error('min_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('common.description') }}</label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $isEdit ? $product->description : '') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                               {{ old('is_active', $isEdit ? $product->is_active : true) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">{{ __('common.is_active') }}</label>
                    </div>
                </div>
            </div>

            {{-- Unit Section --}}
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">{{ __('product.units') }}</h5>
                <button type="button" class="btn btn-success btn-sm" id="addUnitBtn">
                    <i class="bi bi-plus-lg"></i> {{ __('common.add') }} {{ __('product.unit') }}
                </button>
            </div>
            @error('unit_ids')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm align-middle" id="unitTable" style="max-width:400px">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>{{ __('product.unit') }}</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="unitBody">
                        @foreach($selectedUnitObjs as $unitIdx => $unit)
                        <tr data-unit-row="{{ $unitIdx }}">
                            <td class="unit-row-num text-center">{{ $loop->iteration }}</td>
                            <td>
                                <select name="unit_ids[]" class="form-select form-select-sm unit-select" data-unit-row="{{ $unitIdx }}">
                                    @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ $u->id == $unit->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-unit-btn"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Opsi Tambahan (Modifier Groups) Section --}}
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">{{ __('product.modifier_groups') }}</h5>
                <button type="button" class="btn btn-success btn-sm" id="addModifierGroupBtn">
                    <i class="bi bi-plus-lg"></i> {{ __('common.add') }} {{ __('product.modifier_groups') }}
                </button>
            </div>
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-sm align-middle" id="modifierGroupTable" style="max-width:400px">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>{{ __('product.modifier_groups') }}</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="modifierGroupBody">
                        @foreach($selectedModifierGroupObjs as $mg)
                        <tr data-modifier-group-row="{{ $loop->index }}">
                            <td class="modifier-group-row-num text-center">{{ $loop->iteration }}</td>
                            <td>
                                <select name="modifier_group_ids[]" class="form-select form-select-sm modifier-group-select" data-modifier-group-row="{{ $loop->index }}">
                                    @foreach($modifierGroups as $g)
                                    <option value="{{ $g->id }}" {{ $g->id == $mg->id ? 'selected' : '' }}>{{ $g->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-modifier-group-btn"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Variant Section --}}
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">{{ __('product.product_variants') }}</h5>
                <button type="button" class="btn btn-success btn-sm" id="addVariantBtn">
                    <i class="bi bi-plus-lg"></i> {{ __('common.add') }} {{ __('product.product_variant') }}
                </button>
            </div>

            {{-- Container for deleted variant IDs --}}
            <div id="deletedVariants">
                @foreach(old('deleted_variant_ids', []) as $delId)
                <input type="hidden" name="deleted_variant_ids[]" value="{{ $delId }}">
                @endforeach
            </div>

            <div class="table-responsive">
                <table id="variantTable" class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>{{ __('common.name') }} <span class="text-danger">*</span></th>
                            <th>{{ __('common.sku') }}</th>
                            <th>{{ __('product.barcode') }}</th>
                            <th style="width:90px" class="text-center">{{ __('product.track_stock') }}</th>
                            <th>{{ __('product.attributes') }}</th>
                            <th style="width:80px" class="text-center" data-col="is_active">{{ __('common.is_active') }}</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="variantBody">
                        @if($useOldVariants)
                            @foreach($oldVariants as $i => $v)
                            <tr data-variant-idx="{{ $i }}">
                                <td class="row-num text-center">
                                    <span>{{ $loop->iteration }}</span>
                                    @if(!empty($v['id']))
                                    <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $v['id'] }}">
                                    @endif
                                </td>
                                <td><input type="text" name="variants[{{ $i }}][name]" class="form-control form-control-sm variant-name-input" value="{{ $v['name'] ?? '' }}" required></td>
                                <td><input type="text" name="variants[{{ $i }}][sku]" class="form-control form-control-sm" value="{{ $v['sku'] ?? '' }}"></td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text p-1"><input type="checkbox" class="form-check-input barcode-auto-cb" title="{{ __('product.barcode_auto') }}"></div>
                                        <input type="text" name="variants[{{ $i }}][barcode]" class="form-control form-control-sm" value="{{ $v['barcode'] ?? '' }}">
                                    </div>
                                    <input type="hidden" name="variants[{{ $i }}][barcode_auto]" value="0" class="barcode-auto-flag">
                                </td>
                                <td class="text-center"><input type="checkbox" name="variants[{{ $i }}][is_stock_tracked]" value="1" class="form-check-input" {{ !isset($v['is_stock_tracked']) || !empty($v['is_stock_tracked']) ? 'checked' : '' }}></td>
                                <td><textarea name="variants[{{ $i }}][attributes]" class="form-control form-control-sm font-monospace" rows="1" placeholder='{"key":"value"}'>{{ $v['attributes'] ?? '' }}</textarea></td>
                                <td class="text-center" data-col="is_active">
                                    <input type="checkbox" name="variants[{{ $i }}][is_active]" value="1" class="form-check-input" {{ !empty($v['is_active']) ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-variant-btn" data-variant-id="{{ $v['id'] ?? '' }}"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @elseif($isEdit)
                            @foreach($product->variants as $i => $variant)
                            <tr data-variant-idx="{{ $i }}">
                                <td class="row-num text-center">
                                    <span>{{ $loop->iteration }}</span>
                                    <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">
                                </td>
                                <td><input type="text" name="variants[{{ $i }}][name]" class="form-control form-control-sm variant-name-input" value="{{ $variant->name }}" required></td>
                                <td><input type="text" name="variants[{{ $i }}][sku]" class="form-control form-control-sm" value="{{ $variant->sku }}"></td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-text p-1"><input type="checkbox" class="form-check-input barcode-auto-cb" title="{{ __('product.barcode_auto') }}"></div>
                                        <input type="text" name="variants[{{ $i }}][barcode]" class="form-control form-control-sm" value="{{ $variant->barcode }}">
                                    </div>
                                    <input type="hidden" name="variants[{{ $i }}][barcode_auto]" value="0" class="barcode-auto-flag">
                                </td>
                                <td class="text-center"><input type="checkbox" name="variants[{{ $i }}][is_stock_tracked]" value="1" class="form-check-input" {{ $variant->is_stock_tracked ? 'checked' : '' }}></td>
                                <td><textarea name="variants[{{ $i }}][attributes]" class="form-control form-control-sm font-monospace" rows="1" placeholder='{"key":"value"}'>{{ $variant->attributes ? json_encode($variant->attributes) : '' }}</textarea></td>
                                <td class="text-center" data-col="is_active">
                                    <input type="checkbox" name="variants[{{ $i }}][is_active]" value="1" class="form-check-input" {{ $variant->is_active ? 'checked' : '' }}>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-variant-btn" data-variant-id="{{ $variant->id }}"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Variant Prices Section --}}
            <hr>
            <h5 class="mb-3">{{ __('product.variant_prices') }}</h5>
            <div class="table-responsive mb-3">
                <table id="priceTable" class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:180px">{{ __('product.product_variant') }}</th>
                            @foreach($selectedUnitObjs as $unitIdx => $unit)
                            <th data-unit-row="{{ $unitIdx }}" data-unit-id="{{ $unit->id }}">{{ __('common.price') }} ({{ $unit->name }})</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="priceBody">
                        @if($useOldVariants)
                            @foreach($oldVariants as $i => $v)
                            <tr data-variant-idx="{{ $i }}">
                                <td class="variant-name-label align-middle">{{ $v['name'] ?? '' }}</td>
                                @foreach($selectedUnitObjs as $unitIdx => $unit)
                                <td data-unit-row="{{ $unitIdx }}" data-unit-id="{{ $unit->id }}">
                                    <input type="number" name="variants[{{ $i }}][prices][{{ $unit->id }}]"
                                           class="form-control form-control-sm price-input" min="0" step="0.01"
                                           value="{{ $v['prices'][$unit->id] ?? 0 }}">
                                </td>
                                @endforeach
                            </tr>
                            @if($selectedModifierGroupObjs->isNotEmpty())
                            <tr data-modifier-subrow="{{ $i }}" class="table-active">
                                <td colspan="999" class="py-1 px-2">
                                    <div class="d-flex flex-wrap gap-3 align-items-center small">
                                        @foreach($selectedModifierGroupObjs as $group)
                                            @foreach($group->modifiers as $modifier)
                                            @php
                                                $modEnabled = isset($v['modifier_enabled'][$modifier->id]);
                                                $modPrice   = $v['modifier_prices'][$modifier->id] ?? $modifier->default_price;
                                            @endphp
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="checkbox" name="variants[{{ $i }}][modifier_enabled][{{ $modifier->id }}]"
                                                       value="1" class="form-check-input modifier-check"
                                                       data-default-price="{{ $modifier->default_price }}"
                                                       {{ $modEnabled ? 'checked' : '' }}>
                                                <span class="text-nowrap">{{ $modifier->name }}</span>
                                                <input type="number" name="variants[{{ $i }}][modifier_prices][{{ $modifier->id }}]"
                                                       class="form-control form-control-sm modifier-price-input" style="width:90px"
                                                       min="0" step="0.01" value="{{ $modPrice }}"
                                                       {{ !$modEnabled ? 'disabled' : '' }}>
                                            </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        @elseif($isEdit)
                            @foreach($product->variants as $i => $variant)
                            @php $variantPrices = $variant->prices->keyBy('unit_id'); @endphp
                            <tr data-variant-idx="{{ $i }}">
                                <td class="variant-name-label align-middle">{{ $variant->name }}</td>
                                @foreach($selectedUnitObjs as $unitIdx => $unit)
                                <td data-unit-row="{{ $unitIdx }}" data-unit-id="{{ $unit->id }}">
                                    <input type="number" name="variants[{{ $i }}][prices][{{ $unit->id }}]"
                                           class="form-control form-control-sm price-input" min="0" step="0.01"
                                           value="{{ $variantPrices[$unit->id]->price ?? 0 }}">
                                </td>
                                @endforeach
                            </tr>
                            @if($selectedModifierGroupObjs->isNotEmpty())
                            <tr data-modifier-subrow="{{ $i }}" class="table-active">
                                <td colspan="999" class="py-1 px-2">
                                    <div class="d-flex flex-wrap gap-3 align-items-center small">
                                        @foreach($selectedModifierGroupObjs as $group)
                                            @foreach($group->modifiers as $modifier)
                                            @php
                                                $variantMod = $variant->variantModifiers->firstWhere('modifier_id', $modifier->id);
                                                $modEnabled = $variantMod !== null;
                                                $modPrice   = $modEnabled ? $variantMod->price : $modifier->default_price;
                                            @endphp
                                            <div class="d-flex align-items-center gap-1">
                                                <input type="checkbox" name="variants[{{ $i }}][modifier_enabled][{{ $modifier->id }}]"
                                                       value="1" class="form-check-input modifier-check"
                                                       data-default-price="{{ $modifier->default_price }}"
                                                       {{ $modEnabled ? 'checked' : '' }}>
                                                <span class="text-nowrap">{{ $modifier->name }}</span>
                                                <input type="number" name="variants[{{ $i }}][modifier_prices][{{ $modifier->id }}]"
                                                       class="form-control form-control-sm modifier-price-input" style="width:90px"
                                                       min="0" step="0.01" value="{{ $modPrice }}"
                                                       {{ !$modEnabled ? 'disabled' : '' }}>
                                            </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $isEdit ? __('common.update') : __('common.save') }}
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
let variantIdx = {{ $renderedCount }};
let unitRowIdx = {{ $unitRowCount }};
const priceLabel = '{{ __("common.price") }}';
const barcodeAutoPlaceholder = '{{ __("product.barcode_auto") }}';
const unitOptions = `@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach`;
const modifierGroupOptions = `@foreach($modifierGroups as $g)<option value="{{ $g->id }}">{{ $g->name }}</option>@endforeach`;
let modifierGroupRowIdx = {{ $selectedModifierGroupObjs->count() }};

// { unitRow: { id, name } }
const selectedUnits = {};
document.querySelectorAll('.unit-select').forEach(sel => {
    selectedUnits[sel.dataset.unitRow] = { id: sel.value, name: sel.options[sel.selectedIndex].text };
});

// Unit select: change
document.getElementById('unitBody').addEventListener('change', function (e) {
    if (e.target.classList.contains('unit-select')) {
        const r         = e.target.dataset.unitRow;
        const oldUnitId = selectedUnits[r]?.id;
        const newUnitId = e.target.value;
        const newUnitName = e.target.options[e.target.selectedIndex].text;
        selectedUnits[r] = { id: newUnitId, name: newUnitName };
        updatePriceColumn(r, oldUnitId, newUnitId, newUnitName);
        updateMinMax();
    }
});

// Remove unit row
document.getElementById('unitBody').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-unit-btn');
    if (btn) {
        const tr = btn.closest('tr');
        const r  = tr.dataset.unitRow;
        delete selectedUnits[r];
        removePriceColumn(r);
        tr.remove();
        updateUnitRowNumbers();
        updateMinMax();
    }
});

// Add unit row
document.getElementById('addUnitBtn').addEventListener('click', function () {
    const r    = unitRowIdx++;
    const tbody = document.getElementById('unitBody');
    const tr   = document.createElement('tr');
    tr.dataset.unitRow = r;
    tr.innerHTML = `
        <td class="unit-row-num text-center"></td>
        <td><select name="unit_ids[]" class="form-select form-select-sm unit-select" data-unit-row="${r}">${unitOptions}</select></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-unit-btn"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    updateUnitRowNumbers();
    const sel     = tr.querySelector('.unit-select');
    const unitId  = sel.value;
    const unitName = sel.options[sel.selectedIndex].text;
    selectedUnits[r] = { id: unitId, name: unitName };
    addPriceColumn(r, unitId, unitName);
    updateMinMax();
});

function addPriceColumn(unitRow, unitId, unitName) {
    const headerRow = document.querySelector('#priceTable thead tr');
    const th = document.createElement('th');
    th.dataset.unitRow = unitRow;
    th.dataset.unitId  = unitId;
    th.textContent     = `${priceLabel} (${unitName})`;
    headerRow.appendChild(th);

    document.querySelectorAll('#priceBody tr[data-variant-idx]').forEach(row => {
        const idx = row.dataset.variantIdx;
        const td = document.createElement('td');
        td.dataset.unitRow = unitRow;
        td.dataset.unitId  = unitId;
        td.innerHTML = `<input type="number" name="variants[${idx}][prices][${unitId}]" class="form-control form-control-sm price-input" min="0" step="0.01" value="0">`;
        row.appendChild(td);
    });
}

function updatePriceColumn(unitRow, oldUnitId, newUnitId, newUnitName) {
    const th = document.querySelector(`#priceTable thead tr [data-unit-row="${unitRow}"]`);
    if (th) {
        th.dataset.unitId = newUnitId;
        th.textContent    = `${priceLabel} (${newUnitName})`;
    }
    document.querySelectorAll(`#priceBody tr [data-unit-row="${unitRow}"]`).forEach(td => {
        td.dataset.unitId = newUnitId;
        const input = td.querySelector('input');
        if (input) input.name = input.name.replace(`][prices][${oldUnitId}]`, `][prices][${newUnitId}]`);
    });
}

function removePriceColumn(unitRow) {
    document.querySelector(`#priceTable thead tr [data-unit-row="${unitRow}"]`)?.remove();
    document.querySelectorAll(`#priceBody tr [data-unit-row="${unitRow}"]`).forEach(td => td.remove());
}

function updateUnitRowNumbers() {
    document.querySelectorAll('#unitBody .unit-row-num').forEach(function (el, i) {
        el.textContent = i + 1;
    });
}

function updateMinMax() {
    const prices = Array.from(document.querySelectorAll('#priceBody .price-input'))
        .map(inp => parseFloat(inp.value) || 0)
        .filter(v => v > 0);
    const minVal = prices.length ? Math.min(...prices) : 0;
    document.getElementById('min_price_display').value = minVal;
    document.getElementById('min_price_hidden').value  = minVal;
}

document.getElementById('variantBody').addEventListener('input', function (e) {
    if (e.target.classList.contains('variant-name-input')) {
        const idx = e.target.closest('tr').dataset.variantIdx;
        const label = document.querySelector(`#priceBody tr[data-variant-idx="${idx}"] .variant-name-label`);
        if (label) label.textContent = e.target.value || '-';
    }
});

document.getElementById('variantBody').addEventListener('change', function (e) {
    if (e.target.classList.contains('barcode-auto-cb')) {
        const td    = e.target.closest('td');
        const input = td.querySelector('input[type="text"]');
        const flag  = td.querySelector('.barcode-auto-flag');
        if (e.target.checked) {
            input.disabled    = true;
            input.placeholder = barcodeAutoPlaceholder;
            input.value       = '';
            if (flag) flag.value = '1';
        } else {
            input.disabled    = false;
            input.placeholder = '';
            if (flag) flag.value = '0';
        }
    }
});

document.getElementById('priceBody')?.addEventListener('input', function (e) {
    if (e.target.classList.contains('price-input')) updateMinMax();
});

document.getElementById('addVariantBtn').addEventListener('click', function () {
    const i = variantIdx++;
    const row = document.createElement('tr');
    row.dataset.variantIdx = i;
    row.innerHTML = `
        <td class="row-num text-center"><span></span></td>
        <td><input type="text" name="variants[${i}][name]" class="form-control form-control-sm variant-name-input" required></td>
        <td><input type="text" name="variants[${i}][sku]" class="form-control form-control-sm"></td>
        <td><div class="input-group input-group-sm"><div class="input-group-text p-1"><input type="checkbox" class="form-check-input barcode-auto-cb" title="${barcodeAutoPlaceholder}"></div><input type="text" name="variants[${i}][barcode]" class="form-control form-control-sm"></div><input type="hidden" name="variants[${i}][barcode_auto]" value="0" class="barcode-auto-flag"></td>
        <td class="text-center"><input type="checkbox" name="variants[${i}][is_stock_tracked]" value="1" class="form-check-input" checked></td>
        <td><textarea name="variants[${i}][attributes]" class="form-control form-control-sm font-monospace" rows="1" placeholder='{"key":"value"}'></textarea></td>
        <td class="text-center" data-col="is_active"><input type="checkbox" name="variants[${i}][is_active]" value="1" class="form-check-input" checked></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-variant-btn" data-variant-id=""><i class="bi bi-trash"></i></button></td>
    `;
    document.getElementById('variantBody').appendChild(row);

    const priceRow = document.createElement('tr');
    priceRow.dataset.variantIdx = i;
    const tdName = document.createElement('td');
    tdName.className   = 'variant-name-label align-middle text-muted fst-italic';
    tdName.textContent = '-';
    priceRow.appendChild(tdName);
    Object.entries(selectedUnits).forEach(([unitRow, unit]) => {
        const td = document.createElement('td');
        td.dataset.unitRow = unitRow;
        td.dataset.unitId  = unit.id;
        td.innerHTML = `<input type="number" name="variants[${i}][prices][${unit.id}]" class="form-control form-control-sm price-input" min="0" step="0.01" value="0">`;
        priceRow.appendChild(td);
    });
    document.getElementById('priceBody').appendChild(priceRow);
    rebuildVariantSubrows();

    updateRowNumbers();
});

document.getElementById('variantBody').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-variant-btn');
    if (btn) {
        const variantId = btn.dataset.variantId;
        if (variantId) {
            const hidden = document.createElement('input');
            hidden.type  = 'hidden';
            hidden.name  = 'deleted_variant_ids[]';
            hidden.value = variantId;
            document.getElementById('deletedVariants').appendChild(hidden);
        }
        const tr  = btn.closest('tr');
        const idx = tr.dataset.variantIdx;
        tr.remove();
        document.querySelector(`#priceBody tr[data-variant-idx="${idx}"]`)?.remove();
        document.querySelector(`#priceBody tr[data-modifier-subrow="${idx}"]`)?.remove();
        updateRowNumbers();
        updateMinMax();
    }
});

function updateRowNumbers() {
    document.querySelectorAll('#variantBody .row-num span').forEach(function (el, i) {
        el.textContent = i + 1;
    });
}

updateRowNumbers();
updateUnitRowNumbers();
updateMinMax();

// ── Modifier groups (Opsi Tambahan) ───────────────────────────────────────
const allModifierGroupsData = {!! json_encode($modifierGroups->map(fn($g) => [
    'id'        => $g->id,
    'name'      => $g->name,
    'modifiers' => $g->modifiers->map(fn($m) => [
        'id'            => $m->id,
        'name'          => $m->name,
        'default_price' => (float) $m->default_price,
    ])->values(),
])->values()) !!};

let selectedModifierGroups = {!! json_encode($selectedModifierGroupObjs->map(fn($g) => [
    'id'        => $g->id,
    'name'      => $g->name,
    'modifiers' => $g->modifiers->map(fn($m) => [
        'id'            => $m->id,
        'name'          => $m->name,
        'default_price' => (float) $m->default_price,
    ])->values(),
])->values()) !!};

function rebuildVariantSubrows() {
    document.querySelectorAll('#priceBody tr[data-variant-idx]').forEach(function(varRow) {
        const idx = varRow.dataset.variantIdx;
        let subrow = document.querySelector(`#priceBody tr[data-modifier-subrow="${idx}"]`);
        if (selectedModifierGroups.length === 0) {
            subrow?.remove();
            return;
        }
        // Preserve current checked/value state
        const currentState = {};
        if (subrow) {
            subrow.querySelectorAll('.modifier-check').forEach(function(cb) {
                const mId = (cb.name.match(/\[modifier_enabled\]\[(\d+)\]/) || [])[1];
                if (mId) {
                    const priceInput = cb.closest('.d-flex').querySelector('.modifier-price-input');
                    currentState[mId] = { checked: cb.checked, price: priceInput ? priceInput.value : '' };
                }
            });
        }
        if (!subrow) {
            subrow = document.createElement('tr');
            subrow.dataset.modifierSubrow = idx;
            subrow.className = 'table-active';
            const td = document.createElement('td');
            td.colSpan = 999;
            td.className = 'py-1 px-2';
            subrow.appendChild(td);
            varRow.insertAdjacentElement('afterend', subrow);
        }
        let html = '<div class="d-flex flex-wrap gap-3 align-items-center small">';
        selectedModifierGroups.forEach(function(group) {
            group.modifiers.forEach(function(m) {
                const state     = currentState[m.id] || {};
                const isChecked = state.checked || false;
                const priceVal  = state.price !== undefined && state.price !== '' ? state.price : m.default_price;
                html += `<div class="d-flex align-items-center gap-1"><input type="checkbox" name="variants[${idx}][modifier_enabled][${m.id}]" value="1" class="form-check-input modifier-check" data-default-price="${m.default_price}"${isChecked ? ' checked' : ''}><span class="text-nowrap">${m.name}</span><input type="number" name="variants[${idx}][modifier_prices][${m.id}]" class="form-control form-control-sm modifier-price-input" style="width:90px" min="0" step="0.01" value="${priceVal}"${isChecked ? '' : ' disabled'}></div>`;
            });
        });
        html += '</div>';
        subrow.querySelector('td').innerHTML = html;
    });
}

function updateSelectedGroupsFromDOM() {
    const seen = new Set();
    selectedModifierGroups = [];
    document.querySelectorAll('#modifierGroupBody .modifier-group-select').forEach(function(sel) {
        const groupId = parseInt(sel.value);
        if (!groupId || seen.has(groupId)) return;
        seen.add(groupId);
        const group = allModifierGroupsData.find(function(g) { return g.id === groupId; });
        if (group) selectedModifierGroups.push(group);
    });
}

function updateModifierGroupRowNumbers() {
    document.querySelectorAll('#modifierGroupBody .modifier-group-row-num').forEach(function(el, i) {
        el.textContent = i + 1;
    });
}

document.getElementById('addModifierGroupBtn').addEventListener('click', function() {
    const r  = modifierGroupRowIdx++;
    const tr = document.createElement('tr');
    tr.dataset.modifierGroupRow = r;
    tr.innerHTML = `<td class="modifier-group-row-num text-center"></td><td><select name="modifier_group_ids[]" class="form-select form-select-sm modifier-group-select" data-modifier-group-row="${r}">${modifierGroupOptions}</select></td><td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-modifier-group-btn"><i class="bi bi-trash"></i></button></td>`;
    document.getElementById('modifierGroupBody').appendChild(tr);
    updateModifierGroupRowNumbers();
    updateSelectedGroupsFromDOM();
    rebuildVariantSubrows();
});

document.getElementById('modifierGroupBody').addEventListener('change', function(e) {
    if (e.target.classList.contains('modifier-group-select')) {
        updateSelectedGroupsFromDOM();
        rebuildVariantSubrows();
    }
});

document.getElementById('modifierGroupBody').addEventListener('click', function(e) {
    const btn = e.target.closest('.remove-modifier-group-btn');
    if (!btn) return;
    btn.closest('tr').remove();
    updateModifierGroupRowNumbers();
    updateSelectedGroupsFromDOM();
    rebuildVariantSubrows();
});

document.getElementById('priceBody').addEventListener('change', function (e) {
    if (e.target.classList.contains('modifier-check')) {
        const priceInput = e.target.closest('.d-flex').querySelector('.modifier-price-input');
        if (e.target.checked) {
            priceInput.disabled = false;
            if (!parseFloat(priceInput.value)) {
                priceInput.value = e.target.dataset.defaultPrice || 0;
            }
        } else {
            priceInput.disabled = true;
        }
    }
});
</script>
@endpush

@endsection
