@extends('layouts.app')

@php $isEdit = isset($customer); @endphp

@section('title', ($isEdit ? __('common.edit') : __('common.add')) . ' ' . __('customer.customer'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $isEdit ? __('common.edit') : __('common.add') }} {{ __('customer.customer') }}</h4>
    <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card" style="max-width: 550px;">
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('customers.update', $customer) : route('customers.store') }}">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('common.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $isEdit ? $customer->name : '') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">{{ __('common.phone') }}</label>
                <input type="text" name="phone" id="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $isEdit ? $customer->phone : '') }}">
                @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('common.email') }}</label>
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $isEdit ? $customer->email : '') }}">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">{{ __('common.address') }}</label>
                <textarea name="address" id="address" rows="3"
                          class="form-control @error('address') is-invalid @enderror">{{ old('address', $isEdit ? $customer->address : '') }}</textarea>
                @error('address')
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
