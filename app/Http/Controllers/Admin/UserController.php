<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserActionLogged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserController\StoreUserReques;
use App\Http\Requests\Admin\UserController\UpdateUserRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select('id', 'name', 'email')
            ->with([
                'roles:id,name'
            ])
            ->get();
        $roles = Role::select('id', 'name')->get();

        return Inertia::render('Admin/Users/Index', compact('users', 'roles'));
    }

    public function store(StoreUserReques $request)
    {
        event(new UserActionLogged(
            action: 'Crear usuario',
            userId: Auth::id(),
            ipAddress: $request->ip()
        ));

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($validated['role_id']);

        return to_route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        event(new UserActionLogged(
            action: 'Actualizar usuario',
            userId: Auth::id(),
            ipAddress: $request->ip()
        ));

        $validated = $request->validated();

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles($validated['role_id']);

        return to_route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return to_route('admin.users.index');
    }

    public function employee(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'schedule_id')
            ->with([
                'roles:id,name'
            ])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'employee');
            })
            ->get();

        return Inertia::render('Admin/Users/Employee', compact('users'));
    }

    public function backup()
    {
        //echo('test');
        $database = env('DB_DATABASE');
        $backupFileName = $database . '_backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        $backupPath = storage_path('app/public/backups/' . $backupFileName);

        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups', 0755, true);
        }

        $dbDriver = DB::getDriverName();

        if ($dbDriver === 'mysql') {
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $database;
        } elseif ($dbDriver === 'pgsql') {
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
            $tableKey = 'table_name';
        } else {
            $this->error('Unsupported database driver: ' . $dbDriver);
            return;
        }

        $handle = fopen($backupPath, 'w');
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $rows = DB::table($tableName)->get();

            fwrite($handle, 'Table: ' . $tableName . "\n");

            foreach ($rows as $row) {
                $line = implode(',', (array) $row);
                fwrite($handle, $line . "\n");
            }

            fwrite($handle, "\n\n");
        }


        //dd($backupPath);
        fclose($handle);

        return response()->download($backupPath)->deleteFileAfterSend(true);
    }
}
