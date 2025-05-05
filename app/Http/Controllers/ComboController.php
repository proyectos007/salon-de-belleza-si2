<?php

namespace App\Http\Controllers;

use App\Events\UserActionLogged;
use App\Models\Combo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ComboController extends Controller
{
    public function index()
    {
        return Inertia::render('Combos/index', [
            'combos' => Combo::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Combos/create');
    }

    public function store(Request $request)
    {
        event(new UserActionLogged(
            action: 'Crear combo',
            userId: Auth::id(),
            ipAddress: $request->ip()
        ));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'has_discount' => 'boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('combos', 'public');
            $validated['image_path'] = $path;
        }

        if ($request->id) {
            $combo = Combo::find($request->id);
            $combo->update($validated);
        } else {
            $combo = Combo::create($validated);
        }

        if ($request->has('services')) {
            $services = json_decode($request->services);
            $combo->services()->sync($services);
        } else {
            $combo->services()->detach();
        }

        return response()->json([
            'message' => 'Combo actualizado',
            'combo' => $combo
        ]);
    }

    public function edit(Combo $combo)
    {
        event(new UserActionLogged(
            action: 'Editar combo',
            userId: Auth::id(),
            ipAddress: 'Desconocido'
        ));

        return Inertia::render('Combos/create', [
            'combo' => $combo,
            'services' => $combo->services()?->pluck('services.id')->toArray() ?? [],
        ]);
    }

    public function destroy(Combo $combo)
    {
        event(new UserActionLogged(
            action: 'Eliminar combo',
            userId: Auth::id(),
            ipAddress: 'Desconocido'
        ));

        $combo->delete();

        return redirect()->route('combos.index')->with('message', 'Combo eliminado');
    }
}
