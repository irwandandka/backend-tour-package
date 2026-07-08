<?php

namespace App\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ErrorHandler
{
    /**
     * Handle the exception and return the appropriate response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Throwable $e)
    {
        // Log the error
        $this->logError($e);

        // Jika ini adalah exception validasi
        if ($e instanceof ValidationException) {
            return $this->handleValidationException($e);
        }

        // Policy/Gate authorization failure (mis. $this->authorize())
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'message' => $e->getMessage() ?: 'This action is unauthorized.',
            ], Response::HTTP_FORBIDDEN);
        }

        // Route-model-binding atau ::findOrFail() gagal menemukan record
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Resource not found',
            ], Response::HTTP_NOT_FOUND);
        }

        // Exception HTTP eksplisit (404, 403, dll) harus mempertahankan status code-nya
        if ($e instanceof HttpExceptionInterface) {
            return response()->json([
                'message' => $e->getMessage() ?: 'An error occurred',
            ], $e->getStatusCode());
        }

        // Tangani exception lainnya (seperti QueryException, dll)
        return $this->handleGeneralError($e);
    }

    /**
     * Handle ValidationException.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleValidationException(ValidationException $e)
    {
        return response()->json([
            'message' => 'Validation Error',
            'errors' => $e->errors(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Handle general errors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleGeneralError(Throwable $e)
    {
        return response()->json([
            'message' => 'An error occurred',
            'error' => $e->getMessage(),
            'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function logError(Throwable $throwable)
    {
        Log::channel('system-error')->error($throwable->getMessage());
        Log::channel('system-error')->error('File '.$throwable->getFile());
        Log::channel('system-error')->error('Line '.$throwable->getLine());
    }
}
