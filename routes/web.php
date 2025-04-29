<?php

use App\Http\Controllers\ComboController;
use App\Http\Controllers\ServiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'services' => \App\Models\Service::where('has_available', 1)->get(),
        'combos' => \App\Models\Combo::all(),
    ]);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'),])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/services/get-list', [ServiceController::class, 'getList'])->name('services.get-list');
    Route::resource('/services', ServiceController::class);
    Route::resource('/combos', ComboController::class);

    // admin.roles.store 
    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('users', Admin\UserController::class)->except(['show']);
            Route::resource('roles', Admin\RoleController::class)->except(['show']);
            Route::post('/roles/{role}/permissions', [Admin\RoleController::class, 'assignPermissions'])->name('roles.permissions');
            Route::resource('permissions', Admin\PermissionController::class)->except(['show']);
        });
});
