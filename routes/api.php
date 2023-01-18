<?php

use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;
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
Route::post('register', [UsuarioController::class, 'register']);
Route::post('login', [UsuarioController::class, 'login']);

Route::group( ['middleware' => ["auth:sanctum"]], function(){
    //rutas
    Route::controller(UsuarioController::class)->group(function () {
        Route::get('user-profile', 'userProfile');
        Route::post('logout',  'logout');
    });
});

/* Route::resource('vehiculo', VehiculoController::class)->only(['index','show','update','destroy','store']); */
Route::group( ['middleware' => ["auth:sanctum"]], function(){
    //rutas
    Route::controller(VehiculoController::class)->group(function () {
        Route::get('vehiculos','index')->name('index.vehiculos');
        Route::post('vehiculos','store')->name('store.vehiculos');
        Route::post('vehiculos/{id}','update')->name('update.vehiculos');
        Route::delete('vehiculos/{id}','destroy')->name('destroy.vehiculos');
    });
});
/* Route::controller(VehiculoController::class)->group(function () {
    Route::get('vehiculos','index')->middleware('can:index.vehiculos')->name('index.vehiculos');
    Route::post('vehiculos','store')->middleware('can:store.vehiculos')->name('store.vehiculos');
    Route::post('vehiculos/{id}','update')->middleware('can:update.vehiculos')->name('update.vehiculos');
    Route::delete('vehiculos/{id}','destroy')->middleware('can:destroy.vehiculos')->name('destroy.vehiculos');
}); */

