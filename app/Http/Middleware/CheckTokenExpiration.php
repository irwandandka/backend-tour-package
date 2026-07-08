<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $accessToken = $request->bearerToken();

        // Token nggak ada
        if (! $accessToken) {
            return response()->json([
                'status' => 'error',
                'code' => 'token_missing',
                'message' => 'Authorization token is required',
            ], 401);
        }

        // Cari token di DB
        $token = PersonalAccessToken::findToken($accessToken);

        if (! $token) {
            return response()->json([
                'status' => 'error',
                'code' => 'token_invalid',
                'message' => 'Authorization token is invalid',
            ], 401);
        }

        // Token expired?
        if ($token->expires_at && $token->expires_at->isPast()) {
            // Optional → revoke sekalian
            $token->delete();

            return response()->json([
                'status' => 'error',
                'code' => 'token_expired',
                'message' => 'Authorization token has expired',
            ], 401);
        }

        // biar di controller bisa akses auth()->user();
        $request->setUserResolver(function () use ($token) {
            return $token->tokenable; // biasanya model User
        });

        return $next($request);
    }
}
