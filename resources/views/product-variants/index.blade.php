@extends('layouts.app')

@section('title', __('product.product_variants'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('product.product_variants') }}</h4>
    <a href="{{ route('product-variants.create') }}" class="btn btn-primary btn-sm">
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
                <select name="product_id" class="form-select form-select-sm">
                    <option value="">-- {{ __('product.product') }} --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ ($productId ?? '') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary btn-sm">{{ __('common.search') }}</button>
                <a href="{{ route('product-variants.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('common.reset') }}</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('product.product') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.sku') }}</th>
                        <th>{{ __('common.price') }}</th>
                        <th>{{ __('common.stock') }}</th>
                        <th>{{ __('common.is_active') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variants as $variant)
                    <tr>
                        <td>{{ $variants->firstItem() + $loop->index }}</td>
                        <td>{{ $variant->product->name }}</td>
                        <td>{{ $variant->name }}</td>
                        <td>{{ $variant->sku ?? '-' }}</td>
                        <td>{{ $variant->price !== null ? number_format($variant->price, 0, ',', '.') : '-' }}</td>
                        <td>{{ $variant->stock }}</td>
                        <td>
                            @if($variant->is_active)
                                <span class="badge bg-success">{{ __('common.active') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('common.inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('product-variants.edit', $variant) }}" class="btn btn-warning btn-sm">{{ __('common.edit') }}</a>
                            <form method="POST" action="{{ route('product-variants.destroy', $variant) }}" class="d-inline"
                                  onsubmit="return confirm('{{ __('common.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">{{ __('common.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">{{ __('common.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $variants->links() }}
    </div>
</div>
@endsection
