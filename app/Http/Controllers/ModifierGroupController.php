<?php

namespace App\Http\Controllers;

use App\Models\Modifier;
use App\Models\ModifierGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModifierGroupController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $groups = ModifierGroup::withCount('modifiers')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('modifier-groups.index', compact('groups', 'search'));
    }

    public function create()
    {
        return view('modifier-groups.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:100|unique:modifier_groups,name',
            'min_selection'      => 'nullable|integer|min:0',
            'modifiers'          => 'nullable|array',
            'modifiers.*.name'   => 'required|string|max:100',
            'modifiers.*.default_price'  => 'nullable|numeric|min:0',
        ]);

        $group = ModifierGroup::create([
            'name'          => $validated['name'],
            'min_selection' => $validated['min_selection'] ?? 0,
        ]);

        foreach ($request->input('modifiers', []) as $m) {
            if (empty($m['name'])) continue;
            $group->modifiers()->create([
                'name'          => $m['name'],
                'default_price' => $m['default_price'] ?? 0,
            ]);
        }

        return redirect()->route('modifier-groups.index')
            ->with('success', __('common.success_create', ['name' => __('modifier.modifier_group')]));
    }

    public function edit(ModifierGroup $modifierGroup)
    {
        $modifierGroup->load('modifiers');
        return view('modifier-groups.form', compact('modifierGroup'));
    }

    public function update(Request $request, ModifierGroup $modifierGroup)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:100',
                Rule::unique('modifier_groups', 'name')->ignore($modifierGroup->id)],
            'min_selection'      => 'nullable|integer|min:0',
            'modifiers'          => 'nullable|array',
            'modifiers.*.id'     => 'nullable|integer',
            'modifiers.*.name'   => 'required|string|max:100',
            'modifiers.*.default_price'  => 'nullable|numeric|min:0',
            'deleted_modifier_ids'   => 'nullable|array',
            'deleted_modifier_ids.*' => 'nullable|integer',
        ]);

        $modifierGroup->update([
            'name'          => $validated['name'],
            'min_selection' => $validated['min_selection'] ?? 0,
        ]);

        // Delete removed modifiers
        $deletedIds = array_filter((array) $request->input('deleted_modifier_ids', []));
        if ($deletedIds) {
            Modifier::whereIn('id', $deletedIds)
                ->where('modifier_group_id', $modifierGroup->id)
                ->delete();
        }

        // Upsert modifiers
        foreach ($request->input('modifiers', []) as $m) {
            if (empty($m['name'])) continue;
            $data = [
                'name'          => $m['name'],
                'default_price' => $m['default_price'] ?? 0,
            ];
            if (!empty($m['id'])) {
                Modifier::where('id', $m['id'])
                    ->where('modifier_group_id', $modifierGroup->id)
                    ->update($data);
            } else {
                $modifierGroup->modifiers()->create($data);
            }
        }

        return redirect()->route('modifier-groups.index')
            ->with('success', __('common.success_update', ['name' => __('modifier.modifier_group')]));
    }

    public function destroy(ModifierGroup $modifierGroup)
    {
        $modifierGroup->delete();

        return redirect()->route('modifier-groups.index')
            ->with('success', __('common.success_delete', ['name' => __('modifier.modifier_group')]));
    }
}
