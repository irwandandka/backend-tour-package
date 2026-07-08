<?php

namespace App\Services;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ErrorHandler
{
    /**
     * Handle the exception and return the appropriate response.
     *
     * @param  Throwable  $e
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

        // Exception HTTP eksplisit (404, 403, dll) harus mempertahankan status code-nya
        if ($e instanceof HttpExceptionInterface) {
            return response()->json([
                'message' => $e->getMessage() ?: 'An error occurred',
            ], $e->getStatusCode());
        }

        // Tangani exception lainnya (seperti QueryException, ModelNotFoundException, dll)
        return $this->handleGeneralError($e);
    }

    /**
     * Handle ValidationException.
     *
     * @param ValidationException $e
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
     * @param Throwable $e
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
        Log::channel('system-error')->error("File " . $throwable->getFile());
        Log::channel('system-error')->error("Line " . $throwable->getLine());
    }
}
