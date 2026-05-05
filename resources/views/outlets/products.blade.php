@extends('layouts.app')

@section('title', __('outlet.outlet_products') . ' - ' . $outlet->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('outlet.outlet_products') }}: {{ $outlet->name }}</h4>
    <a href="{{ route('outlets.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('outlets.products.sync', $outlet) }}">
            @csrf

            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:50px;">
                                <input type="checkbox" id="checkAll" class="form-check-input">
                            </th>
                            <th>{{ __('common.code') }}</th>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('product.category') }}</th>
                            <th>{{ __('product.sell_price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                       class="form-check-input product-check"
                                       {{ $product->is_assigned ? 'checked' : '' }}>
                            </td>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                            <td>{{ number_format($product->sell_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">{{ __('common.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary mt-2">{{ __('outlet.sync_products') }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('checkAll').addEventListener('change', function () {
        document.querySelectorAll('.product-check').forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
