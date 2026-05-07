@extends('layouts.app')

@section('title', __('common.add') . ' ' . __('product.product'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('common.add') }} {{ __('product.product') }}</h4>
    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code" class="form-label">{{ __('common.code') }}</label>
                        <input type="text" name="code" id="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code') }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('common.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">{{ __('product.category') }} <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- {{ __('product.category') }} --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="unit_id" class="form-label">{{ __('product.unit') }} <span class="text-danger">*</span></label>
                        <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                            <option value="">-- {{ __('product.unit') }} --</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('common.description') }}</label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="base_price" class="form-label">{{ __('product.base_price') }} <span class="text-danger">*</span></label>
                        <input type="number" name="base_price" id="base_price" min="0" step="0.01"
                               class="form-control @error('base_price') is-invalid @enderror"
                               value="{{ old('base_price', 0) }}" required>
                        @error('base_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                               {{ old('is_active', true) ? 'checked' : '' }}>
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
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>{{ __('common.name') }} <span class="text-danger">*</span></th>
                            <th>{{ __('common.sku') }}</th>
                            <th>{{ __('product.buy_price') }}</th>
                            <th>{{ __('product.sell_price') }}</th>
                            <th style="width:100px">{{ __('common.stock') }}</th>
                            <th style="width:80px" class="text-center">{{ __('common.is_active') }}</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="variantBody">
                        @php $oldVariants = old('variants', []); @endphp
                        @foreach($oldVariants as $i => $v)
                        <tr>
                            <td class="row-num text-center">{{ $loop->iteration }}</td>
                            <td><input type="text" name="variants[{{ $i }}][name]" class="form-control form-control-sm" value="{{ $v['name'] ?? '' }}" required></td>
                            <td><input type="text" name="variants[{{ $i }}][sku]" class="form-control form-control-sm" value="{{ $v['sku'] ?? '' }}"></td>
                            <td><input type="number" name="variants[{{ $i }}][buy_price]" class="form-control form-control-sm" min="0" step="0.01" value="{{ $v['buy_price'] ?? '' }}"></td>
                            <td><input type="number" name="variants[{{ $i }}][sell_price]" class="form-control form-control-sm" min="0" step="0.01" value="{{ $v['sell_price'] ?? '' }}"></td>
                            <td><input type="number" name="variants[{{ $i }}][min_stock]" class="form-control form-control-sm" min="0" value="{{ $v['min_stock'] ?? 0 }}"></td>
                            <td class="text-center">
                                <input type="checkbox" name="variants[{{ $i }}][is_active]" value="1" class="form-check-input" {{ !empty($v['is_active']) ? 'checked' : '' }}>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-variant-btn"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
let variantIdx = {{ count(old('variants', [])) }};

document.getElementById('addVariantBtn').addEventListener('click', function () {
    const i = variantIdx++;
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="row-num text-center"></td>
        <td><input type="text" name="variants[${i}][name]" class="form-control form-control-sm" required></td>
        <td><input type="text" name="variants[${i}][sku]" class="form-control form-control-sm"></td>
        <td><input type="number" name="variants[${i}][buy_price]" class="form-control form-control-sm" min="0" step="0.01"></td>
        <td><input type="number" name="variants[${i}][sell_price]" class="form-control form-control-sm" min="0" step="0.01"></td>
        <td><input type="number" name="variants[${i}][min_stock]" class="form-control form-control-sm" min="0" value="0"></td>
        <td class="text-center"><input type="checkbox" name="variants[${i}][is_active]" value="1" class="form-check-input" checked></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-variant-btn"><i class="bi bi-trash"></i></button></td>
    `;
    document.getElementById('variantBody').appendChild(row);
    updateRowNumbers();
});

document.getElementById('variantBody').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-variant-btn');
    if (btn) {
        btn.closest('tr').remove();
        updateRowNumbers();
    }
});

function updateRowNumbers() {
    document.querySelectorAll('#variantBody .row-num').forEach(function (el, i) {
        el.textContent = i + 1;
    });
}

updateRowNumbers();
</script>
@endpush

@endsection
