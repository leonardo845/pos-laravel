@extends('layouts.app')

@php $isEdit = isset($productCategory); @endphp

@section('title', ($isEdit ? __('common.edit') : __('common.add')) . ' ' . __('product.product_category'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $isEdit ? __('common.edit') : __('common.add') }} {{ __('product.product_category') }}</h4>
    <a href="{{ route('product-categories.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('product-categories.update', $productCategory) : route('product-categories.store') }}">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('common.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $isEdit ? $productCategory->name : '') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $isEdit ? __('common.update') : __('common.save') }}
            </button>
        </form>
    </div>
</div>
@endsection
