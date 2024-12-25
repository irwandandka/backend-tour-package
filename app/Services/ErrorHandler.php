<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class ErrorHandler
{
    /**
     * General function to handle errors and return consistent responses.
     */
    public function handleError(Exception $exception)
    {
        // Log the error (optional)
        Log::channel('system-error')->error($exception->getMessage());
        Log::channel('system-error')->error("File " . $exception->getFile());
        Log::channel('system-error')->error("Line " . $exception->getLine());

        // Check the type of exception and return an appropriate response
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            // Handle ModelNotFoundException (e.g., 404 errors)
            return response()->json([
                'error' => 'Resource not found.',
                'message' => $exception->getMessage(),
            ], 404);
        }

        // For validation errors (422 Unprocessable Entity)
        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'error' => 'Validation error.',
                'message' => $exception->errors(),
            ], 422);
        }

        // For generic exceptions (500 Internal Server Error)
        return response()->json([
            'error' => 'Server error.',
            'message' => 'Something went wrong. Please try again later.',
        ], 500);
    }
}
