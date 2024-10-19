<?php

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

            Log::info($tokenInfo['token']);

            // Check if the token is valid and not expired
            if (!$tokenInfo || $tokenInfo->isExpired()) {
                // Optionally log the token expiration
                if ($tokenInfo) {
                    $tokenInfo->delete();
                }

                Log::info('Token expired or invalid.');
                return response()->json(['message' => 'Invalid or expired token'], 401);
            }

            // Attach the token information to the request for easy access
            $request->attributes->add(['tokenInfo' => $tokenInfo]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Exception during token validation: ' . $e->getMessage());
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }
    }
}
