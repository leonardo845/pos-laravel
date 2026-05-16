@extends('layouts.app')

@php $isEdit = isset($supplier); @endphp

@section('title', ($isEdit ? __('common.edit') : __('common.add')) . ' ' . __('supplier.supplier'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $isEdit ? __('common.edit') : __('common.add') }} {{ __('supplier.supplier') }}</h4>
    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card" style="max-width: 550px;">
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="mb-3">
                <label for="name" class="form-label">
                    {{ __('common.name') }} <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $isEdit ? $supplier->name : '') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">{{ __('supplier.phone_number') }}</label>
                <input type="text" name="phone_number" id="phone_number"
                       class="form-control @error('phone_number') is-invalid @enderror"
                       value="{{ old('phone_number', $isEdit ? $supplier->phone_number : '') }}">
                @error('phone_number')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('common.email') }}</label>
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $isEdit ? $supplier->email : '') }}">
                @error('email')
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
