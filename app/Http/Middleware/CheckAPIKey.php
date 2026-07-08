<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAPIKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        $expectedApiKey = config('services.internal.api_key');

        if ($request->is('api/v1/auth/google/callback')) {
            return $next($request);
        }

        if (empty($expectedApiKey) || ! is_string($apiKey) || ! hash_equals($expectedApiKey, $apiKey)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
