<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $roles = Role::when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->withCount('users')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('roles.index', compact('roles', 'search'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:roles,slug',
        ]);

        Role::create($validated);

        return redirect()->route('roles.index')
            ->with('success', __('common.success_create', ['name' => __('user.role')]));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => ['required', 'string', 'max:100', Rule::unique('roles', 'slug')->ignore($role->id)],
        ]);

        $role->update($validated);

        return redirect()->route('roles.index')
            ->with('success', __('common.success_update', ['name' => __('user.role')]));
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', __('common.success_delete', ['name' => __('user.role')]));
    }
}
