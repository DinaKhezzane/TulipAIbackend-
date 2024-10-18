<?php// app/Exceptions/Handler.php

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

public function render($request, Exception $exception)
{
    // If the request expects JSON and an exception occurs
    if ($request->expectsJson()) {
        // Handle validation exceptions
        if ($exception instanceof ValidationException) {
            return response()->json([
                'errors' => $exception->validator->errors(),
            ], 422);
        }

        // Return JSON for other exceptions
        return response()->json([
            'error' => 'Something went wrong',
            'message' => $exception->getMessage(),
        ], 500);
    }

    // For non-JSON requests, let Laravel handle it normally
    return parent::render($request, $exception);
}
