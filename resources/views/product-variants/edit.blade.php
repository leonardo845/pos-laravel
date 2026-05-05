@extends('layouts.app')

@section('title', __('common.edit') . ' ' . __('product.product_variant'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('common.edit') }} {{ __('product.product_variant') }}</h4>
    <a href="{{ route('product-variants.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card" style="max-width: 550px;">
    <div class="card-body">
        <form method="POST" action="{{ route('product-variants.update', $productVariant) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label for="product_id" class="form-label">{{ __('product.product') }}</label>
                <select name="product_id" id="product_id"
                        class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">-- {{ __('product.product') }} --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id', $productVariant->product_id) == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                    @endforeach
                </select>
                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('common.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $productVariant->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="sku" class="form-label">{{ __('common.sku') }}</label>
                <input type="text" name="sku" id="sku"
                       class="form-control @error('sku') is-invalid @enderror"
                       value="{{ old('sku', $productVariant->sku) }}">
                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">{{ __('common.price') }}</label>
                <input type="number" name="price" id="price" min="0" step="0.01"
                       class="form-control @error('price') is-invalid @enderror"
                       value="{{ old('price', $productVariant->price) }}">
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">{{ __('common.stock') }}</label>
                <input type="number" name="stock" id="stock" min="0"
                       class="form-control @error('stock') is-invalid @enderror"
                       value="{{ old('stock', $productVariant->stock) }}" required>
                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                       {{ old('is_active', $productVariant->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('common.is_active') }}</label>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
        </form>
    </div>
</div>
@endsection
