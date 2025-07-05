<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register'])
    ->middleware('validate.register');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('validate.login');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->middleware('validate.forgot.password');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('validate.reset.password');

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])
        ->middleware('validate.update.profile');
        
    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->middleware('validate.change.password');
        
    // Rutas para gestión de archivos
    Route::prefix('files')->group(function () {
        Route::get('/', [FileController::class, 'index']);
        Route::delete('/{id}', [FileController::class, 'destroy']);
    });
}); 