<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidateDeleteAccount
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:1',
            'confirm_deletion' => 'required|boolean|accepted',
        ], [
            'password.required' => 'La contraseña es obligatoria para eliminar la cuenta',
            'password.string' => 'La contraseña debe ser una cadena de texto',
            'password.min' => 'La contraseña no puede estar vacía',
            'confirm_deletion.required' => 'Debes confirmar que quieres eliminar tu cuenta',
            'confirm_deletion.boolean' => 'La confirmación debe ser verdadera o falsa',
            'confirm_deletion.accepted' => 'Debes aceptar la eliminación de tu cuenta',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        return $next($request);
    }
} 