<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\RoleStoreRequest; // Buat request ini
use App\Http\Requests\RoleUpdateRequest; // Buat request ini
use App\Http\Requests\PermissionStoreRequest; // Buat request ini
use App\Http\Requests\PermissionUpdateRequest; // Buat request ini

class RolePermissionController extends Controller
{
    // --- ROLE CRUD ---

    /**
     * Display a listing of the roles.
     */
    public function indexRole(): View
    {
        $roles = Role::with('permissions')->paginate(10);

        return view('sistem.role_permission.index_role', [
            'title' => 'Manajemen Role',
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function createRole(): View
    {
        $permissions = Permission::all();

        return view('sistem.role_permission.create_role', [
            'title' => 'Tambah Role Baru',
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created role in storage.
     */
    public function storeRole(RoleStoreRequest $request): RedirectResponse
    {
        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions') && !empty($request->permissions)) {
            // Ambil model Permission berdasarkan ID yang dipilih
            $selectedPermissions = Permission::whereIn('id', $request->permissions)->get();
            // Gunakan model Permission untuk sync
            $role->syncPermissions($selectedPermissions);
        }

        return redirect()->route('role_permission.index_role')
            ->with('success', 'Role (' . $role->name . ') berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function editRole(Role $role): View
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('sistem.role_permission.edit_role', [
            'title' => 'Edit Role - ' . $role->name,
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update the specified role in storage.
     */
    public function updateRole(RoleUpdateRequest $request, Role $role): RedirectResponse
    {
        $role->update(['name' => $request->name]);
        if ($request->has('permissions') && !empty($request->permissions)) {
            // Ambil model Permission berdasarkan ID yang dipilih
            $selectedPermissions = Permission::whereIn('id', $request->permissions)->get();
            // Gunakan model Permission untuk sync
            $role->syncPermissions($selectedPermissions);
        } else {
            // Jika tidak ada permission yang dipilih, hapus semua
            $role->syncPermissions([]);
        }

        return redirect()->route('role_permission.index_role')
            ->with('success', 'Role (' . $role->name . ') berhasil diperbarui.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroyRole(Role $role): RedirectResponse
    {
        // Cegah penghapusan role superadmin
        if ($role->name === 'superadmin') {
            return back()->with('error', 'Tidak dapat menghapus role superadmin.');
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('role_permission.index_role')
            ->with('success', 'Role (' . $roleName . ') berhasil dihapus.');
    }

    // --- PERMISSION CRUD ---

    /**
     * Display a listing of the permissions.
     */
    public function indexPermission(): View
    {
        $permissions = Permission::paginate(20);

        return view('sistem.role_permission.index_permission', [
            'title' => 'Manajemen Permission',
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show the form for creating a new permission.
     */
    public function createPermission(): View
    {
        return view('sistem.role_permission.create_permission', [
            'title' => 'Tambah Permission Baru',
        ]);
    }

    /**
     * Store a newly created permission in storage.
     */
    public function storePermission(PermissionStoreRequest $request): RedirectResponse
    {
        $permission = Permission::create(['name' => $request->name]);

        return redirect()->route('role_permission.index_permission')
            ->with('success', 'Permission (' . $permission->name . ') berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function editPermission(Permission $permission): View
    {
        return view('sistem.role_permission.edit_permission', [
            'title' => 'Edit Permission - ' . $permission->name,
            'permission' => $permission,
        ]);
    }

    /**
     * Update the specified permission in storage.
     */
    public function updatePermission(PermissionUpdateRequest $request, Permission $permission): RedirectResponse
    {
        $permission->update(['name' => $request->name]);

        return redirect()->route('role_permission.index_permission')
            ->with('success', 'Permission (' . $permission->name . ') berhasil diperbarui.');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroyPermission(Permission $permission): RedirectResponse
    {
        // Cegah penghapusan permission yang sedang digunakan
        $rolesUsingPermission = $permission->roles;
        if ($rolesUsingPermission->count() > 0) {
            $roleNames = $rolesUsingPermission->pluck('name')->implode(', ');
            return back()->with('error', 'Tidak dapat menghapus permission (' . $permission->name . ') karena sedang digunakan oleh role: ' . $roleNames);
        }

        $permissionName = $permission->name;
        $permission->delete();

        return redirect()->route('role_permission.index_permission')
            ->with('success', 'Permission (' . $permissionName . ') berhasil dihapus.');
    }
}
