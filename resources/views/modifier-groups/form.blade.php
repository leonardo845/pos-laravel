@extends('layouts.app')

@php
    $isEdit       = isset($modifierGroup);
    $renderedCount = $isEdit ? $modifierGroup->modifiers->count() : 0;
    $oldModifiers  = old('modifiers');
    if ($oldModifiers !== null) {
        $renderedCount = count($oldModifiers);
    }
@endphp

@section('title', ($isEdit ? __('common.edit') : __('common.add')) . ' ' . __('modifier.modifier_group'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ $isEdit ? __('common.edit') : __('common.add') }} {{ __('modifier.modifier_group') }}</h4>
    <a href="{{ route('modifier-groups.index') }}" class="btn btn-secondary btn-sm">{{ __('common.cancel') }}</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('modifier-groups.update', $modifierGroup) : route('modifier-groups.store') }}">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('common.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $isEdit ? $modifierGroup->name : '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="min_selection" class="form-label">{{ __('modifier.min_selection') }}</label>
                        <input type="number" name="min_selection" id="min_selection"
                               class="form-control @error('min_selection') is-invalid @enderror"
                               min="0" value="{{ old('min_selection', $isEdit ? $modifierGroup->min_selection : 0) }}">
                        @error('min_selection')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">{{ __('modifier.modifiers') }}</h5>
                <button type="button" class="btn btn-success btn-sm" id="addModifierBtn">
                    <i class="bi bi-plus-lg"></i> {{ __('common.add') }} {{ __('modifier.modifier') }}
                </button>
            </div>

            <div id="deletedModifiers">
                @foreach(old('deleted_modifier_ids', []) as $delId)
                <input type="hidden" name="deleted_modifier_ids[]" value="{{ $delId }}">
                @endforeach
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle" id="modifierTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>{{ __('common.name') }} <span class="text-danger">*</span></th>
                            <th style="width:200px">{{ __('common.price') }}</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="modifierBody">
                        @if($oldModifiers !== null)
                            @foreach($oldModifiers as $i => $m)
                            <tr data-idx="{{ $i }}">
                                <td class="row-num text-center">{{ $loop->iteration }}</td>
                                @if(!empty($m['id']))
                                <input type="hidden" name="modifiers[{{ $i }}][id]" value="{{ $m['id'] }}">
                                @endif
                                <td><input type="text" name="modifiers[{{ $i }}][name]" class="form-control form-control-sm" value="{{ $m['name'] ?? '' }}" required></td>
                                <td><input type="number" name="modifiers[{{ $i }}][default_price]" class="form-control form-control-sm" min="0" step="0.01" value="{{ $m['default_price'] ?? 0 }}"></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-modifier-btn" data-modifier-id="{{ $m['id'] ?? '' }}"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @elseif($isEdit)
                            @foreach($modifierGroup->modifiers as $i => $modifier)
                            <tr data-idx="{{ $i }}">
                                <td class="row-num text-center">{{ $loop->iteration }}</td>
                                <input type="hidden" name="modifiers[{{ $i }}][id]" value="{{ $modifier->id }}">
                                <td><input type="text" name="modifiers[{{ $i }}][name]" class="form-control form-control-sm" value="{{ $modifier->name }}" required></td>
                                <td><input type="number" name="modifiers[{{ $i }}][default_price]" class="form-control form-control-sm" min="0" step="0.01" value="{{ $modifier->default_price }}"></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-modifier-btn" data-modifier-id="{{ $modifier->id }}"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $isEdit ? __('common.update') : __('common.save') }}
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let modifierIdx = {{ $renderedCount }};

document.getElementById('addModifierBtn').addEventListener('click', function () {
    const i = modifierIdx++;
    const tbody = document.getElementById('modifierBody');
    const tr = document.createElement('tr');
    tr.dataset.idx = i;
    tr.innerHTML = `
        <td class="row-num text-center"></td>
        <td><input type="text" name="modifiers[${i}][name]" class="form-control form-control-sm" required></td>
        <td><input type="number" name="modifiers[${i}][default_price]" class="form-control form-control-sm" min="0" step="0.01" value="0"></td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-modifier-btn" data-modifier-id=""><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    updateRowNumbers();
});

document.getElementById('modifierBody').addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-modifier-btn');
    if (btn) {
        const modifierId = btn.dataset.modifierId;
        if (modifierId) {
            const hidden = document.createElement('input');
            hidden.type  = 'hidden';
            hidden.name  = 'deleted_modifier_ids[]';
            hidden.value = modifierId;
            document.getElementById('deletedModifiers').appendChild(hidden);
        }
        btn.closest('tr').remove();
        updateRowNumbers();
    }
});

function updateRowNumbers() {
    document.querySelectorAll('#modifierBody tr').forEach((row, idx) => {
        const numEl = row.querySelector('.row-num');
        if (numEl) numEl.textContent = idx + 1;
    });
}
</script>
@endpush
