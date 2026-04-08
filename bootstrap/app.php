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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                $errors = $e->validator->errors()->getMessages();
                $formattedErrors = [];

                foreach ($errors as $field => $messages) {
                    $formattedErrors[$field] = array_map(function ($message) use ($field) {
                        // Custom Indonesian message for 'required' rule
                        if (str_contains(strtolower($message), 'required') || str_contains(strtolower($message), 'field is missing')) {
                            return "field $field belum di isi";
                        }
                        return $message;
                    }, $messages);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $formattedErrors
                ], 422);
            }
        });
    })->create();
