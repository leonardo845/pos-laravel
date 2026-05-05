@extends('layouts.app')

@section('title', __('common.edit') . ' ' . __('product.product_unit'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('common.edit') }} {{ __('product.product_unit') }}</h4>
    <a href="{{ route('product-units.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" action="{{ route('product-units.update', $productUnit) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('common.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $productUnit->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">{{ __('common.update') }}</button>
        </form>
    </div>
</div>
@endsection
