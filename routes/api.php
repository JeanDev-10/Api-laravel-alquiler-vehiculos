<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
/* Route::resource('vehiculo', VehiculoController::class)->only(['index','show','update','destroy','store']); */
Route::get('vehiculo',[VehiculoController::class,'index']);
Route::post('vehiculo',[VehiculoController::class,'store']);
Route::get('vehiculo/{id}',[VehiculoController::class,'show']);
Route::post('vehiculo/{id}',[VehiculoController::class,'update']);
Route::delete('vehiculo/{id}',[VehiculoController::class,'destroy']);
