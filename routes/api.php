<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register'])
    ->middleware('validate.register');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('validate.login');

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $fileUploadService = new \App\Services\FileUploadService();
        $currentAvatarUrl = $fileUploadService->getUserAvatarUrl($user);
        
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'avatar_url' => $currentAvatarUrl,
            'role' => $user->role,
            'subscription_status' => $user->subscription_status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]);
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])
        ->middleware('validate.update.profile');
        
    // Rutas para gestión de archivos
    Route::prefix('files')->group(function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();
            $files = $user->activeFiles()->latest()->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'files' => $files->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'type' => $file->type,
                            'original_name' => $file->original_name,
                            'public_url' => $file->full_url,
                            'file_size' => $file->formatted_size,
                            'uploaded_at' => $file->created_at->diffForHumans(),
                            'metadata' => $file->metadata,
                        ];
                    })
                ]
            ]);
        });
        
        Route::delete('/{id}', function (Request $request, $id) {
            $user = $request->user();
            $file = $user->fileUploads()->findOrFail($id);
            
            $fileUploadService = new \App\Services\FileUploadService();
            $deleted = $fileUploadService->deleteFile($file);
            
            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? 'Archivo eliminado exitosamente' : 'Error al eliminar el archivo'
            ]);
        });
    });
}); 