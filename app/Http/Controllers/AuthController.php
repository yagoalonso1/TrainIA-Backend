<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PasswordResetService;
use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // Crear el usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Por defecto es user
            'subscription_status' => 'free', // Por defecto es free
        ]);

        // Generar y guardar la URL del avatar automáticamente
        $user->avatar_url = $user->generateAvatarUrl();
        $user->save();

        // Enviar email de bienvenida
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            // Si falla el envío, solo loguea el error (no interrumpe el registro)
            \Log::error('Error enviando email de bienvenida: ' . $e->getMessage());
        }

        // Crear token de acceso con Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url,
                    'role' => $user->role,
                    'subscription_status' => $user->subscription_status,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        // Buscar el usuario por email
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Crear token de acceso con Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $user->avatar_url,
                    'role' => $user->role,
                    'subscription_status' => $user->subscription_status,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // Revocar el token actual del usuario autenticado
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $updated = false;
        $nameChanged = false;

        // Actualizar nombre si se proporciona
        if ($request->has('name') && $request->name !== $user->name) {
            $user->name = $request->name;
            $nameChanged = true;
            $updated = true;
        }

        // Actualizar email si se proporciona
        if ($request->has('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            $updated = true;
        }

        // Manejar subida de avatar usando el servicio
        if ($request->hasFile('avatar')) {
            try {
                $fileUploadService = new \App\Services\FileUploadService();
                $fileUpload = $fileUploadService->uploadFile(
                    $request->file('avatar'),
                    $user,
                    'avatar'
                );
                
                $updated = true;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir el avatar: ' . $e->getMessage()
                ], 422);
            }
        } else if ($nameChanged && str_contains($user->avatar_url, 'ui-avatars.com')) {
            // Si cambió el nombre y tiene avatar generado automáticamente, regenerarlo
            $user->avatar_url = $user->generateAvatarUrl();
            $updated = true;
        }

        if ($updated) {
            $user->save();
        }

        // Obtener la URL del avatar actual (subido o generado)
        $fileUploadService = new \App\Services\FileUploadService();
        $currentAvatarUrl = $fileUploadService->getUserAvatarUrl($user);

        return response()->json([
            'success' => true,
            'message' => $updated ? 'Perfil actualizado exitosamente' : 'No hay cambios que actualizar',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar_url' => $currentAvatarUrl,
                    'role' => $user->role,
                    'subscription_status' => $user->subscription_status,
                ],
                'files' => $updated && $request->hasFile('avatar') ? [
                    'avatar' => [
                        'id' => $fileUpload->id ?? null,
                        'original_name' => $fileUpload->original_name ?? null,
                        'file_size' => $fileUpload->formatted_size ?? null,
                        'uploaded_at' => $fileUpload->created_at->diffForHumans() ?? null,
                    ]
                ] : null,
            ]
        ], 200);
    }

    /**
     * Get current user profile
     */
    public function getUser(Request $request)
    {
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
    }

    /**
     * Send forgot password request
     */
    public function forgotPassword(Request $request)
    {
        try {
            $passwordResetService = new PasswordResetService();
            $result = $passwordResetService->sendForgotPassword($request->email);
            
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request)
    {
        try {
            $passwordResetService = new PasswordResetService();
            $result = $passwordResetService->resetPassword(
                $request->email,
                $request->token,
                $request->password
            );
            
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
