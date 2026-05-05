@extends('layouts.app')

@section('title', __('common.edit') . ' ' . __('product.product'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('common.edit') }} {{ __('product.product') }}</h4>
    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code" class="form-label">{{ __('common.code') }}</label>
                        <input type="text" name="code" id="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $product->code) }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('common.name') }}</label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">{{ __('product.category') }}</label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- {{ __('product.category') }} --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="unit_id" class="form-label">{{ __('product.unit') }}</label>
                        <select name="unit_id" id="unit_id" class="form-select @error('unit_id') is-invalid @enderror">
                            <option value="">-- {{ __('product.unit') }} --</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">{{ __('common.description') }}</label>
                        <textarea name="description" id="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="buy_price" class="form-label">{{ __('product.buy_price') }}</label>
                        <input type="number" name="buy_price" id="buy_price" min="0" step="0.01"
                               class="form-control @error('buy_price') is-invalid @enderror"
                               value="{{ old('buy_price', $product->buy_price) }}" required>
                        @error('buy_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="sell_price" class="form-label">{{ __('product.sell_price') }}</label>
                        <input type="number" name="sell_price" id="sell_price" min="0" step="0.01"
                               class="form-control @error('sell_price') is-invalid @enderror"
                               value="{{ old('sell_price', $product->sell_price) }}" required>
                        @error('sell_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">{{ __('common.is_active') }}</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
        </form>
    </div>
</div>
@endsection
