<?php

namespace App\Http\Middleware;

use App\Http\Middleware\BaseValidator;
use Illuminate\Http\Request;

class ValidateResetPassword extends BaseValidator
{
    protected function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El formato del email no es válido',
            'email.exists' => 'No existe una cuenta con este email',
            'token.required' => 'El token es obligatorio',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password_confirmation.required' => 'La confirmación de contraseña es obligatoria',
        ];
    }
} 