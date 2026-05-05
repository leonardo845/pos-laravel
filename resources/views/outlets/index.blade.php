@extends('layouts.app')

@section('title', __('outlet.outlets'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('outlet.outlets') }}</h4>
    <a href="{{ route('outlets.create') }}" class="btn btn-primary btn-sm">
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
                <a href="{{ route('outlets.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.reset') }}</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.phone') }}</th>
                        <th>{{ __('common.email') }}</th>
                        <th>{{ __('common.is_active') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outlets as $outlet)
                    <tr>
                        <td>{{ $outlets->firstItem() + $loop->index }}</td>
                        <td>{{ $outlet->name }}</td>
                        <td>{{ $outlet->phone ?? '-' }}</td>
                        <td>{{ $outlet->email ?? '-' }}</td>
                        <td>
                            @if($outlet->is_active)
                                <span class="badge bg-success">{{ __('common.active') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('common.inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('outlets.edit', $outlet) }}" class="btn btn-warning btn-sm">{{ __('common.edit') }}</a>
                            <a href="{{ route('outlets.products', $outlet) }}" class="btn btn-info btn-sm">{{ __('outlet.outlet_products') }}</a>
                            <form method="POST" action="{{ route('outlets.destroy', $outlet) }}" class="d-inline"
                                  onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{ __('common.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted">{{ __('common.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $outlets->links() }}
    </div>
</div>
@endsection
