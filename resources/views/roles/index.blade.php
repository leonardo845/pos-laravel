@extends('layouts.app')

@section('title', __('user.roles'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('user.roles') }}</h4>
    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> {{ __('common.add') }}
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="{{ __('common.search') }}..." value="{{ $search ?? '' }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary btn-sm">{{ __('common.search') }}</button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.reset') }}</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.slug') }}</th>
                        <th>{{ __('user.users') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>{{ $roles->firstItem() + $loop->index }}</td>
                        <td>{{ $role->name }}</td>
                        <td><code>{{ $role->slug }}</code></td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-sm">{{ __('common.edit') }}</a>
                            <form method="POST" action="{{ route('roles.destroy', $role) }}" class="d-inline"
                                  onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{ __('common.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">{{ __('common.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $roles->links() }}
    </div>
</div>
@endsection
