<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Default Values
    |--------------------------------------------------------------------------
    |
    | Valores por defecto para usuarios nuevos
    |
    */
    'user' => [
        'default_role' => 'user',
        'default_subscription_status' => 'free',
        'avatar' => [
            'default_size' => 200,
            'default_colors' => [
                'FF6B6B', '4ECDC4', 'FFD93D', 'FF8C42', 
                'C44569', '6C5CE7', 'FD79A8', 'FDCB6E'
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Requirements
    |--------------------------------------------------------------------------
    |
    | Requisitos de fortaleza para contraseñas
    |
    */
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'strength_labels' => [
            'very_weak' => 'Muy débil',
            'weak' => 'Débil',
            'medium' => 'Media',
            'strong' => 'Fuerte',
            'very_strong' => 'Muy fuerte',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configuración para subida de archivos
    |
    */
    'files' => [
        'max_size' => 10240, // 10MB en KB
        'allowed_types' => [
            'avatar' => ['image/jpeg', 'image/png', 'image/gif'],
            'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'exercise_video' => ['video/mp4', 'video/avi', 'video/mov'],
            'exercise_image' => ['image/jpeg', 'image/png', 'image/gif'],
        ],
        'folders' => [
            'avatar' => 'avatars',
            'document' => 'documents',
            'exercise_video' => 'exercise_videos',
            'exercise_image' => 'exercise_images',
            'default' => 'uploads',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Settings
    |--------------------------------------------------------------------------
    |
    | Configuración para emails
    |
    */
    'email' => [
        'temporary_password_length' => 12,
        'password_reset_expiry' => 3600, // 1 hora en segundos
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@trainia.com'),
        'from_name' => env('MAIL_FROM_NAME', 'TrainIA'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Configuración de seguridad
    |
    */
    'security' => [
        'session_lifetime' => 120, // minutos
        'max_login_attempts' => 5,
        'lockout_duration' => 15, // minutos
    ],
]; 