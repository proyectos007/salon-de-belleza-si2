<?php

use App\Http\Controllers\ComboController;
use App\Http\Controllers\ServiceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\ScheduleController;
use Inertia\Inertia;

include 'api.php';

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

    Route::get('/services/get-list', [ServiceController::class, 'getList'])->name('services.get-list')->middleware('NotClientMiddleware');
    Route::resource('/services', ServiceController::class)->middleware('NotClientMiddleware');
    Route::resource('/combos', ComboController::class)->middleware('NotClientMiddleware');
    Route::resource('/schedules', ScheduleController::class)->only(['store'])->middleware('NotClientMiddleware');
    Route::get('/schedules/get-list', [ScheduleController::class, 'getList'])->name('schedules.list')->middleware('NotClientMiddleware');
    Route::post('/schedules/user/update', [ScheduleController::class, 'updateUser'])->name('schedules.user.update')->middleware('NotClientMiddleware');
    
    // admin.roles.store 
    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('users', Admin\UserController::class)->except(['show'])->middleware('NotClientMiddleware');
            Route::get('/users/employee', [Admin\UserController::class, 'employee'])->name('users.employee')->middleware('NotClientMiddleware');
            Route::resource('roles', Admin\RoleController::class)->except(['show'])->middleware('NotClientMiddleware');
            Route::post('/roles/{role}/permissions', [Admin\RoleController::class, 'assignPermissions'])->name('roles.permissions')->middleware('NotClientMiddleware');
            Route::resource('permissions', Admin\PermissionController::class)->except(['show'])->middleware('NotClientMiddleware');
            Route::resource('clients', Admin\ClientController::class)->middleware('NotClientMiddleware');
            Route::get('/backup', [Admin\UserController::class, 'backup'])->name('backup')->middleware('NotClientMiddleware');
        });
});
