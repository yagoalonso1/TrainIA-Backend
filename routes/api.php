<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register'])
    ->middleware('validate.register');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('validate.login');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 