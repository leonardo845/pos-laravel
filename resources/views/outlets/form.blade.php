@extends('layouts.app')

@php $isEdit = isset($outlet); @endphp

@section('title', ($isEdit ? __('common.edit') : __('common.add')) . ' ' . __('outlet.outlet'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $isEdit ? __('common.edit') : __('common.add') }} {{ __('outlet.outlet') }}</h4>
    <a href="{{ route('outlets.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('outlets.update', $outlet) : route('outlets.store') }}">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('common.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $isEdit ? $outlet->name : '') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">{{ __('common.phone') }}</label>
                <input type="text" name="phone" id="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $isEdit ? $outlet->phone : '') }}">
                @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('common.email') }}</label>
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $isEdit ? $outlet->email : '') }}">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">{{ __('common.address') }}</label>
                <textarea name="address" id="address" rows="3"
                          class="form-control @error('address') is-invalid @enderror">{{ old('address', $isEdit ? $outlet->address : '') }}</textarea>
                @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                       {{ old('is_active', $isEdit ? $outlet->is_active : true) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">{{ __('common.is_active') }}</label>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $isEdit ? __('common.update') : __('common.save') }}
            </button>
        </form>
    </div>
</div>
@endsection
