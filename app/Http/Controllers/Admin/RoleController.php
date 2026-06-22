<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(fn($p) => explode('-', $p->name)[0]);
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')->with('status', 'Rol creado exitosamente.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(fn($p) => explode('-', $p->name)[0]);
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => "required|string|max:50|unique:roles,name,{$role->id}",
            'permissions' => 'nullable|array',
        ]);

        if ($role->name !== 'SADM') { // Don't rename SADM
            $role->update(['name' => $validated['name']]);
        }

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('status', 'Rol actualizado.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['SADM'])) {
            return back()->withErrors(['error' => 'No puedes eliminar el rol SADM.']);
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => 'No puedes eliminar un rol con usuarios asignados.']);
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('status', 'Rol eliminado.');
    }
}
