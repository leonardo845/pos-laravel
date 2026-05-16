@extends('layouts.app')

@php $isEdit = isset($role); @endphp

@section('title', ($isEdit ? __('common.edit') : __('common.add')) . ' ' . __('user.role'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $isEdit ? __('common.edit') : __('common.add') }} {{ __('user.role') }}</h4>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('roles.update', $role) : route('roles.store') }}">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('common.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $isEdit ? $role->name : '') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">{{ __('common.slug') }}</label>
                <input type="text" name="slug" id="slug"
                       class="form-control @error('slug') is-invalid @enderror"
                       value="{{ old('slug', $isEdit ? $role->slug : '') }}" required>
                @if(!$isEdit)
                <div class="form-text">e.g. superadmin, owner, admin, cashier</div>
                @endif
                @error('slug')
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
