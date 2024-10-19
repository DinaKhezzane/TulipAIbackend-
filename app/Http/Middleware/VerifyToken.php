<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Token;
use Illuminate\Support\Facades\Log;

namespace App\Http\Middleware;

use Closure;
use App\Models\Token;
use Illuminate\Support\Facades\Log;

class VerifyToken
{
    public function handle($request, Closure $next)
    {
        Log::info('VerifyToken middleware starting successfully');

        // Get the token from the request
        $token = $request->bearerToken();

        // Check if the token is provided
        if (!$token) {
            return response()->json(['message' => 'No token provided'], 401);
        }

        try {
            // Find the token in the database
            $tokenInfo = Token::where('token', $token)->first();

            // Check if the token is valid and not expired
            if (!$tokenInfo || $tokenInfo->isExpired()) {
                // If the token is invalid or expired, delete it from the database
                if ($tokenInfo) {
                    $tokenInfo->delete(); // Optionally log this action if necessary
                }

                Log::info('Token expired or invalid. User needs to reauthenticate.');

                return response()->json(['message' => 'Invalid or expired token'], 401);
            }

            // Optionally, you can add the token information to the request for easy access in controllers
            $request->attributes->add(['tokenInfo' => $tokenInfo]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Exception during token validation: ' . $e->getMessage());
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }
    }
}
