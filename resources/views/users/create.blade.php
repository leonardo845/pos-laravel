@extends('layouts.app')

@section('title', __('common.add') . ' ' . __('user.user'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('common.add') }} {{ __('user.user') }}</h4>
    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="mb-3">
                <label for="role_id" class="form-label">{{ __('user.role') }}</label>
                <select name="role_id" id="role_id"
                        class="form-select @error('role_id') is-invalid @enderror" required>
                    <option value="">-- {{ __('user.role') }} --</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
                @error('role_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('common.name') }}</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">{{ __('auth.username') }}</label>
                <input type="text" name="username" id="username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username') }}" required>
                @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('auth.password') }}</label>
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">{{ __('auth.password_confirm') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
        </form>
    </div>
</div>
@endsection
