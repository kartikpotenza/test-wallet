<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        return $this->handleException($request, $e);
    }

    public function handleException($request, Throwable $exception): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($exception instanceof MethodNotAllowedHttpException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Please try with other request type (POST, PUT, GET, DELETE).',
                    'fieldName' => 'API',
                    'errors' => $exception->getMessage(),
                ], 405 );
            }

            return response()->view('error', ['error' => $exception, 'status' => $exception->getStatusCode() ,'message' => 'The specified method for the request is invalid']);
        }

        if ($exception instanceof NotFoundHttpException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'The API endpoint is invalid.',
                    'fieldName' => 'endpoint',
                    'errors' => $exception->getMessage(),
                ], 404 );
            }
        }

        if ($exception instanceof HttpException) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'The authenticated user is not allowed to access the specified API endpoint.',
                    'fieldName' => 'role',
                    'errors' => $exception->getMessage(),
                ], $exception->getStatusCode() );
            }
        }

        if ($exception instanceof AuthenticationException) {
            if ($request->is('api/*')) {
                 return response()->json([
                    'message' => 'Please re-login to the system.',
                    'fieldName' => 'token',
                    'errors' => $exception->getMessage(),
                ], 400);
            }
        }
    }
}
