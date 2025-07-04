<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
}
