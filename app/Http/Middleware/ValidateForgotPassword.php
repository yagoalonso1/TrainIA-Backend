<?php

namespace App\Http\Middleware;

use App\Http\Middleware\BaseValidator;
use Illuminate\Http\Request;

class ValidateForgotPassword extends BaseValidator
{
    protected function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El formato del email no es válido',
            'email.exists' => 'No existe una cuenta con este email',
        ];
    }
} 