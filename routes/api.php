<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TareaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);



Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('auth/profile', [AuthController::class, 'profile']);
    Route::post('auth/logout', [AuthController::class, 'logout']);


    Route::apiResource('users', UserController::class);
    Route::apiResource('tareas', TareaController::class);

});


