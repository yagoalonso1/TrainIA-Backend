<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ValidateUpdateProfile extends BaseValidator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . auth()->id(),
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ];

        $messages = [
            'name.required' => 'El nombre es obligatorio',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe tener un formato válido',
            'email.unique' => 'Este email ya está registrado por otro usuario',
            'avatar.image' => 'El archivo debe ser una imagen',
            'avatar.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif',
            'avatar.max' => 'La imagen no puede ser mayor a 2MB',
        ];

        // Si la validación falla, el método validate retorna una respuesta JSON de error
        $validationResult = self::validate($request, $rules, $messages);
        if ($validationResult !== null) {
            return $validationResult;
        }

        // Si la validación es exitosa, continuar con la cadena de middlewares
        return $next($request);
    }
}
