<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserActionLogged;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function store(Request $request)
    {
        event(new UserActionLogged(
            action: 'Crear permiso',
            userId: Auth::id(),
            ipAddress: $request->ip()
        ));

        $request->validate(['name' => 'required|string|max:50|unique:permissions,name']);
        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return to_route('admin.roles.index');
    }

    public function update(Request $request, Permission $permission)
    {
        event(new UserActionLogged(
            action: 'Editar permiso',
            userId: Auth::id(),
            ipAddress: $request->ip()
        ));

        $request->validate([
            'name' => 'required|string|max:50|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return to_route('admin.roles.index');
    }

    public function destroy(Permission $permission)
    {
        event(new UserActionLogged(
            action: 'Eliminar permiso',
            userId: Auth::id(),
            ipAddress: 'Desconocida',
        ));

        $permission->delete();

        return to_route('admin.roles.index');
    }
}
