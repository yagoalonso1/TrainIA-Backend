<?php

namespace App\Http\Middleware;

use App\Http\Middleware\BaseValidator;
use Illuminate\Http\Request;

class ValidateChangePassword extends BaseValidator
{
    protected function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|different:current_password',
            'new_password_confirmation' => 'required|string|same:new_password',
        ];
    }

    protected function messages(): array
    {
        return [
            'current_password.required' => 'La contraseña actual es obligatoria',
            'new_password.required' => 'La nueva contraseña es obligatoria',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
            'new_password.different' => 'La nueva contraseña debe ser diferente a la actual',
            'new_password_confirmation.required' => 'La confirmación de contraseña es obligatoria',
            'new_password_confirmation.same' => 'Las contraseñas no coinciden',
        ];
    }
} 