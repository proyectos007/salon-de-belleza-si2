<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ServiceController;

Route::get('/user', function (Request $request) {return $request->user();})->middleware('auth:sanctum');
Route::post('v1/login', [ApiController::class, 'login'])->name('apilogin');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/services/get-list', [ServiceController::class, 'getList'])->name('services.get-list');
});