<?php

use App\Exceptions\DefinitionInvalid;
use App\Exceptions\DemoLimitReached;
use App\Exceptions\InvalidState;
use App\Exceptions\RevisionConflict;
use App\Http\ApiError;
use App\Http\Middleware\AssignRequestId;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1'
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(AssignRequestId::class);
        $middleware->append(SecurityHeaders::class);
        $middleware->api(prepend: [ForceJsonResponse::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Every exception on api/* becomes the error envelope (API design §5.2, §19.8).
        // Laravel has already converted ModelNotFound (404), Authorization (403)
        // and TokenMismatch (419) into HTTP exceptions before this runs.
        $exceptions->render(function (Throwable $e, Request $request): ?JsonResponse {
            // Not an API request, or an exception that already carries its
            // own response: let Laravel handle it.
            if (! $request->is('api/*') || $e instanceof HttpResponseException) {
                return null;
            }

            // The first matching arm wins, so specific classes come first
            // and the generic 500 is the default.
            return match (true) {
                $e instanceof ValidationException => ApiError::response(422, 'validation_failed', 'The given data was invalid.', [
                    'fields' => $e->errors(),
                ]),

                $e instanceof AuthenticationException => ApiError::response(401, 'unauthenticated', 'Please sign in first.'),

                $e instanceof RevisionConflict => ApiError::response(409, 'revision_conflict', $e->getMessage(), $e->details),

                $e instanceof InvalidState => ApiError::response(409, 'invalid_state', $e->getMessage(), [
                    'status' => $e->status,
                    'allowed_from' => $e->allowedFrom,
                ]),

                $e instanceof DefinitionInvalid => ApiError::response(422, 'definition_invalid', $e->getMessage(), [
                    'errors' => $e->errors,
                ]),

                $e instanceof DemoLimitReached => ApiError::response(429, 'demo_limit_reached', $e->getMessage(), [], [
                    'Retry-After' => $e->retryAfter,
                ]),

                // Fixed messages: getMessage() on a converted ModelNotFoundException
                // would leak model class names and IDs. getHeaders() keeps
                // Retry-After, X-RateLimit-* and Allow.
                $e instanceof HttpExceptionInterface => ApiError::response(
                    $e->getStatusCode(),
                    ...match ($e->getStatusCode()) {
                        403 => ['code' => 'forbidden', 'message' => 'You are not allowed to do this.'],
                        404 => ['code' => 'not_found', 'message' => 'Not found.'],
                        405 => ['code' => 'method_not_allowed', 'message' => 'This method is not allowed here.'],
                        419 => ['code' => 'csrf_mismatch', 'message' => 'Your session expired. Refresh and try again.'],
                        429 => ['code' => 'rate_limited', 'message' => 'Too many requests. Try again later.'],
                        503 => ['code' => 'maintenance', 'message' => 'FlowSync is under maintenance. Try again shortly.'],
                        default => ['code' => 'http_error', 'message' => 'The request could not be processed.'],
                    },
                    headers: $e->getHeaders(),
                ),

                // Never getMessage() here: it may hold SQL, paths or secrets.
                default => ApiError::response(500, 'server_error', 'Something went wrong.'),
            };
        });
    })->create();
