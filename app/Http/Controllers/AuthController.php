<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'avatar_id' => $request->avatar_id,
            'role' => 'user', // Por defecto es user
            'subscription_status' => 'free', // Por defecto es free
        ]);

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
                    'avatar_id' => $user->avatar_id,
                    'role' => $user->role,
                    'subscription_status' => $user->subscription_status,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }
}
