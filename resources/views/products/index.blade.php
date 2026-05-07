@extends('layouts.app')

@section('title', __('product.products'))

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('product.products') }}</h4>
    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> {{ __('common.add') }}
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-auto">
                <select id="filterCategory" class="form-select form-select-sm">
                    <option value="">-- {{ __('product.category') }} --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button id="btnReset" class="btn btn-outline-secondary btn-sm">{{ __('common.reset') }}</button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="productsTable" class="table table-bordered table-hover table-sm w-100">
                <thead class="table-dark">
                    <tr>
                        <th>{{ __('common.code') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('product.category') }}</th>
                        <th>{{ __('product.unit') }}</th>
                        <th>{{ __('product.base_price') }}</th>
                        <th>{{ __('common.is_active') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    var table = $('#productsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('products.index') }}',
            data: function (d) {
                d.category_id = $('#filterCategory').val();
            }
        },
        columns: [
            { data: 'code',          name: 'code' },
            { data: 'name',          name: 'name' },
            { data: 'category_name', name: 'category_name', orderable: false, searchable: false },
            { data: 'unit_name',     name: 'unit_name',     orderable: false, searchable: false },
            { data: 'base_price',    name: 'base_price' },
            { data: 'is_active',     name: 'is_active',     orderable: false, searchable: false },
            { data: 'actions',       name: 'actions',       orderable: false, searchable: false },
        ],
        order: [[1, 'asc']],
        language: {
            search: '{{ __('common.search') }}:',
            lengthMenu: '_MENU_',
            info: '_START_-_END_ / _TOTAL_',
            paginate: {
                previous: '&lsaquo;',
                next: '&rsaquo;',
            }
        }
    });

    $('#filterCategory').on('change', function () {
        table.ajax.reload();
    });

    $('#btnReset').on('click', function () {
        $('#filterCategory').val('');
        table.search('').ajax.reload();
    });
});
</script>
@endpush
