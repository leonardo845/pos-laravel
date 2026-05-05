@extends('layouts.app')

@section('title', __('common.add') . ' ' . __('product.product_variant'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('common.add') }} {{ __('product.product_variant') }}</h4>
    <a href="{{ route('product-variants.index', $productId ? ['product_id' => $productId] : []) }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card" style="max-width: 550px;">
    <div class="card-body">
        <form method="POST" action="{{ route('product-variants.store') }}">
            @csrf

            <div class="mb-3">
                <label for="product_id" class="form-label">{{ __('product.product') }}</label>
                <select name="product_id" id="product_id"
                        class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">-- {{ __('product.product') }} --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id', $productId) == $product->id ? 'selected' : '' }}>
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
                       value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="sku" class="form-label">{{ __('common.sku') }}</label>
                <input type="text" name="sku" id="sku"
                       class="form-control @error('sku') is-invalid @enderror"
                       value="{{ old('sku') }}">
                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="buy_price" class="form-label">{{ __('product.buy_price') }}</label>
                <input type="number" name="buy_price" id="buy_price" min="0" step="0.01"
                       class="form-control @error('buy_price') is-invalid @enderror"
                       value="{{ old('buy_price') }}">
                @error('buy_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="sell_price" class="form-label">{{ __('product.sell_price') }}</label>
                <input type="number" name="sell_price" id="sell_price" min="0" step="0.01"
                       class="form-control @error('sell_price') is-invalid @enderror"
                       value="{{ old('sell_price') }}">
                @error('sell_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label for="min_stock" class="form-label">{{ __('common.min_stock') }}</label>
                <input type="number" name="min_stock" id="min_stock" min="0"
                       class="form-control @error('min_stock') is-invalid @enderror"
                       value="{{ old('min_stock', 0) }}" required>
                @error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                       {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('common.is_active') }}</label>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
        </form>
    </div>
</div>
@endsection
