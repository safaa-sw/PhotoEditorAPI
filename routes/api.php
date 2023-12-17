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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('photo',[\App\Http\Controllers\PhotoEditingController::class,'index']);
Route::post('photo/crop',[\App\Http\Controllers\PhotoEditingController::class,'crop']);
Route::post('photo/resize',[\App\Http\Controllers\PhotoEditingController::class,'resize']);
Route::post('photo/contrast',[\App\Http\Controllers\PhotoEditingController::class,'contrast']);
Route::post('photo/bright',[\App\Http\Controllers\PhotoEditingController::class,'bright']);
Route::post('photo/opacity',[\App\Http\Controllers\PhotoEditingController::class,'opacity']);
Route::post('photo/sharp',[\App\Http\Controllers\PhotoEditingController::class,'sharp']);
Route::post('photo/rotate',[\App\Http\Controllers\PhotoEditingController::class,'rotate']);
Route::post('photo/saturate',[\App\Http\Controllers\PhotoEditingController::class,'saturate']);
Route::post('photo/3deffect',[\App\Http\Controllers\PhotoEditingController::class,'add3dEffect']);
Route::post('photo/flip',[\App\Http\Controllers\PhotoEditingController::class,'flip']);
Route::post('photo/invertcolor',[\App\Http\Controllers\PhotoEditingController::class,'invertColor']);


