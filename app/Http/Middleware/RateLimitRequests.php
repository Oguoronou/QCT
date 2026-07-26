<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

/**
 * Rate Limiting Middleware
 * Protège contre les abus et attaques brute-force
 */
class RateLimitRequests
{
    public function __construct(private RateLimiter $limiter)
    {
    }

    public function handle(Request $request, Closure $next, $limit = 60, $minutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $limit)) {
            return response()->json([
                'message' => 'Trop de requêtes. Veuillez réessayer plus tard.',
            ], 429);
        }

        $this->limiter->hit($key, $minutes * 60);

        return $next($request);
    }

    protected function resolveRequestSignature(Request $request)
    {
        return sha1(implode('|', [
            $request->method(),
            $request->getHost(),
            $request->ip(),
        ]));
    }
}
