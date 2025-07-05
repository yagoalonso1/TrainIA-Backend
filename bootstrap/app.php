<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'validate.register' => \App\Http\Middleware\ValidateRegister::class,
            'validate.login' => \App\Http\Middleware\ValidateLogin::class,
            'validate.update.profile' => \App\Http\Middleware\ValidateUpdateProfile::class,
            'validate.forgot.password' => \App\Http\Middleware\ValidateForgotPassword::class,
            'validate.reset.password' => \App\Http\Middleware\ValidateResetPassword::class,
            'validate.change.password' => \App\Http\Middleware\ValidateChangePassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
