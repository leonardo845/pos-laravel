<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $suppliers = Supplier::select('id', 'name', 'phone_number', 'email')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create()
    {
        return view('suppliers.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:150',
            'phone_number' => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:100|unique:suppliers,email',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', __('common.success_create', ['name' => __('supplier.supplier')]));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:150',
            'phone_number' => 'nullable|string|max:30',
            'email'        => ['nullable', 'email', 'max:100',
                Rule::unique('suppliers', 'email')->ignore($supplier->id)],
        ]);

        $validated['updated_by'] = Auth::id();

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', __('common.success_update', ['name' => __('supplier.supplier')]));
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->update(['deleted_by' => Auth::id()]);
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', __('common.success_delete', ['name' => __('supplier.supplier')]));
    }
}
