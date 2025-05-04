<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserController\StoreUserReques;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $clients = User::role('client')->get();
        return Inertia::render('Admin/Clients/Index', compact('clients'));
    }

    public function store(StoreUserReques $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles('client');

        return to_route('admin.clients.index')->with('success', 'Cliente creado exitosamente.');
    }
}
