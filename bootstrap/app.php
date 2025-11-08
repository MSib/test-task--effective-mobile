<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: '/',
        // web: __DIR__.'/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        // health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function ($exceptions) {
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            return new JsonResponse([
                'status'  => 'error',
                'message' => 'Resource not found',
            ], 404);
        });

        $exceptions->render(function (ValidationException $e, $request) {
            return new JsonResponse([
                'message' => 'Validation Error',
                'errors'  => $e->errors(),
            ], 422);
        });

        $exceptions->render(function (Throwable $e, $request) {
            return new JsonResponse([
                'message' => 'Internal Server Error',
            ], 500);
        });
    })->create();
