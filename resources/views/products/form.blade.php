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

                    <div class="mb-3">
                        <label class="form-label">{{ __('product.units') }}</label>
                        @error('unit_ids')<div class="text-danger small mb-1">{{ $message }}</div>@enderror
                        <div class="border rounded p-2" style="max-height:160px;overflow-y:auto">
                            @forelse($units as $unit)
                            <div class="form-check">
                                <input type="checkbox" name="unit_ids[]" id="unit_{{ $unit->id }}"
                                       value="{{ $unit->id }}" class="form-check-input unit-checkbox"
                                       data-unit-name="{{ $unit->name }}"
                                       {{ in_array($unit->id, array_map('intval', (array) old('unit_ids', $defaultUnitIds))) ? 'checked' : '' }}>
                                <label class="form-check-label" for="unit_{{ $unit->id }}">{{ $unit->name }}</label>
                            </div>
                            @empty
                            <span class="text-muted small">{{ __('common.no_data') }}</span>
                            @endforelse
                        </div>
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
                            @foreach($selectedUnitObjs as $unit)
                            <th data-unit-id="{{ $unit->id }}">{{ __('common.price') . ' (' . $unit->name . ')' }}</th>
                            @endforeach
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
                                <td><input type="text" name="variants[{{ $i }}][name]" class="form-control form-control-sm" value="{{ $v['name'] ?? '' }}" required></td>
                                <td><input type="text" name="variants[{{ $i }}][sku]" class="form-control form-control-sm" value="{{ $v['sku'] ?? '' }}"></td>
                                @foreach($selectedUnitObjs as $unit)
                                <td data-unit-id="{{ $unit->id }}">
                                    <input type="number" name="variants[{{ $i }}][prices][{{ $unit->id }}]"
                                           class="form-control form-control-sm price-input" min="0" step="0.01"
                                           value="{{ $v['prices'][$unit->id] ?? 0 }}">
                                </td>
                                @endforeach
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
                            @php $variantPrices = $variant->prices->keyBy('product_unit_id'); @endphp
                            <tr data-variant-idx="{{ $i }}">
                                <td class="row-num text-center">
                                    <span>{{ $loop->iteration }}</span>
                                    <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">
                                </td>
                                <td><input type="text" name="variants[{{ $i }}][name]" class="form-control form-control-sm" value="{{ $variant->name }}" required></td>
                                <td><input type="text" name="variants[{{ $i }}][sku]" class="form-control form-control-sm" value="{{ $variant->sku }}"></td>
                                @foreach($selectedUnitObjs as $unit)
                                <td data-unit-id="{{ $unit->id }}">
                                    <input type="number" name="variants[{{ $i }}][prices][{{ $unit->id }}]"
                                           class="form-control form-control-sm price-input" min="0" step="0.01"
                                           value="{{ $variantPrices[$unit->id]->price ?? 0 }}">
                                </td>
                                @endforeach
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

            <button type="submit" class="btn btn-primary">
                {{ $isEdit ? __('common.update') : __('common.save') }}
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
let variantIdx = {{ $renderedCount }};

// {unitId: unitName} for currently selected units
const selectedUnits = {};
document.querySelectorAll('.unit-checkbox:checked').forEach(cb => {
    selectedUnits[cb.value] = cb.dataset.unitName;
});

// Toggle unit price columns when checkbox changes
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('unit-checkbox')) {
        const unitId   = e.target.value;
        const unitName = e.target.dataset.unitName;
        if (e.target.checked) {
            selectedUnits[unitId] = unitName;
            addPriceColumn(unitId, unitName);
        } else {
            delete selectedUnits[unitId];
            removePriceColumn(unitId);
        }
        updateMinMax();
    }
});

function addPriceColumn(unitId, unitName) {
    const headerRow = document.querySelector('#variantTable thead tr');
    const activeTh  = headerRow.querySelector('[data-col="is_active"]');
    const th = document.createElement('th');
    th.dataset.unitId = unitId;
    th.textContent    = unitName;
    headerRow.insertBefore(th, activeTh);

    document.querySelectorAll('#variantBody tr').forEach(row => {
        const activeTd = row.querySelector('[data-col="is_active"]');
        const idx      = row.dataset.variantIdx;
        const td = document.createElement('td');
        td.dataset.unitId = unitId;
        td.innerHTML = `<input type="number" name="variants[${idx}][prices][${unitId}]" class="form-control form-control-sm price-input" min="0" step="0.01" value="0">`;
        row.insertBefore(td, activeTd);
    });
    updateMinMax();
}

function removePriceColumn(unitId) {
    document.querySelector(`#variantTable thead tr [data-unit-id="${unitId}"]`)?.remove();
    document.querySelectorAll(`#variantBody tr [data-unit-id="${unitId}"]`).forEach(td => td.remove());
    updateMinMax();
}

function updateMinMax() {
    const prices = Array.from(document.querySelectorAll('#variantBody .price-input'))
        .map(inp => parseFloat(inp.value) || 0)
        .filter(v => v > 0);
    const minVal = prices.length ? Math.min(...prices) : 0;
    document.getElementById('min_price_display').value = minVal;
    document.getElementById('min_price_hidden').value  = minVal;
}

document.getElementById('variantBody').addEventListener('input', function (e) {
    if (e.target.classList.contains('price-input')) updateMinMax();
});

document.getElementById('addVariantBtn').addEventListener('click', function () {
    const i = variantIdx++;
    const row = document.createElement('tr');
    row.dataset.variantIdx = i;

    let priceCols = '';
    Object.entries(selectedUnits).forEach(([unitId]) => {
        priceCols += `<td data-unit-id="${unitId}"><input type="number" name="variants[${i}][prices][${unitId}]" class="form-control form-control-sm price-input" min="0" step="0.01" value="0"></td>`;
    });

    row.innerHTML = `
        <td class="row-num text-center"><span></span></td>
        <td><input type="text" name="variants[${i}][name]" class="form-control form-control-sm" required></td>
        <td><input type="text" name="variants[${i}][sku]" class="form-control form-control-sm"></td>
        ${priceCols}
        <td class="text-center" data-col="is_active"><input type="checkbox" name="variants[${i}][is_active]" value="1" class="form-check-input" checked></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-variant-btn" data-variant-id=""><i class="bi bi-trash"></i></button></td>
    `;
    document.getElementById('variantBody').appendChild(row);
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
        btn.closest('tr').remove();
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
updateMinMax();
</script>
@endpush

@endsection
