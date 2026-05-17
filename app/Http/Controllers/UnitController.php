<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $units = Unit::select('id', 'name')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('units.index', compact('units', 'search'));
    }

    public function create()
    {
        return view('units.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:units,name',
        ]);

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('success', __('common.success_create', ['name' => __('product.unit')]));
    }

    public function edit(Unit $unit)
    {
        return view('units.form', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100',
                Rule::unique('units', 'name')->ignore($unit->id)],
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', __('common.success_update', ['name' => __('product.unit')]));
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', __('common.success_delete', ['name' => __('product.unit')]));
    }
}

