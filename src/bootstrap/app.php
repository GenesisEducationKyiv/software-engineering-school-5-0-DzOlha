<?php

use App\Exceptions\CustomException;
use App\Modules\Observability\Application\Metrics\Middleware\MetricsMiddleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(MetricsMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([

        ]);

        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);


        $exceptions->reportable(function (Throwable $e) {
            //
        });

        //A helper function to generate consistent JSON responses
        $jsonResponse = function ($error = true, $message = '', $data = null, $statusCode = 500) {
            $response = [
                'success' => !$error,
                'message' => $message,
            ];

            if ($data !== null) {
                $response['data'] = $data;
            }

            return response()->json($response, $statusCode);
        };

        $exceptions->renderable(function (CustomException $e, $request) use ($jsonResponse) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $jsonResponse(true, $e->getMessage(), null, $e->getStatusCode());
            }
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) use ($jsonResponse) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $jsonResponse(true, 'Resource not found.', null, 404);
            }
        });

        $exceptions->renderable(function (ModelNotFoundException $e, $request) use ($jsonResponse) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $jsonResponse(true, 'Resource not found.', null, 404);
            }
        });

        $exceptions->renderable(function (AuthorizationException $e, $request) use ($jsonResponse) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $jsonResponse(true, 'You are not authorized to access this resource.', null, 403);
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, $request) use ($jsonResponse) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $jsonResponse(true, 'Unauthenticated.', null, 401);
            }
        });

        $exceptions->renderable(function (MethodNotAllowedHttpException $e, $request) use ($jsonResponse) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $jsonResponse(true, 'Method not allowed.', null, 405);
            }
        });

        $exceptions->renderable(function (ThrottleRequestsException $e, $request) use ($jsonResponse) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $jsonResponse(true, 'Too many requests.', null, 429);
            }
        });

        $exceptions->renderable(function (TokenMismatchException $e, $request) use ($jsonResponse) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $jsonResponse(true, 'CSRF token mismatch.', null, 419);
            }
        });

        $exceptions->renderable(function (Throwable $e, $request) use ($jsonResponse) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $message = config('app.debug') ? $e->getMessage() : 'Server Error';
                return $jsonResponse(true, $message, null, 500);
            }
        });

    })
    ->create();
