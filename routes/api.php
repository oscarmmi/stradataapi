<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('auth/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::group([
    'middleware' => 'jwt.verify',
    'prefix' => 'auth'
], function ($router) {    
    Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    Route::post('refresh', [\App\Http\Controllers\AuthController::class, 'refresh'])->name('refresh');
    Route::post('me', [\App\Http\Controllers\AuthController::class, 'me'])->name('me');
    Route::post('busquedaCoincidencias', [\App\Http\Controllers\busquedaCoincidenciasController::class, 'index'])->name('busquedaCoincidencias');
    Route::post('logCoincidencias', [\App\Http\Controllers\logCoincidenciasController::class, 'index'])->name('logCoincidencias');    
});


