<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiExecutionTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $executionTime = round((microtime(true) - $startTime) * 1000, 2); // In milliseconds

        $routePath = $request->path();
        $method = $request->getMethod();
        $statusCode = $response->getStatusCode();

        // Log APIs matching the pattern (products, categories, users, orders list and detail endpoints)
        if ($this->shouldLogApiTiming($routePath)) {
            Log::info("API Execution Time", [
                'method' => $method,
                'path' => $routePath,
                'status' => $statusCode,
                'execution_time_ms' => $executionTime,
            ]);
        }

        return $response;
    }

    /**
     * Check if the route should be logged for execution time
     *
     * @param string $path
     * @return bool
     */
    private function shouldLogApiTiming(string $path): bool
    {
        $patterns = [
            'api/products',
            'api/categories',
            'api/users',
            'api/orders',
        ];

        foreach ($patterns as $pattern) {
            if (strpos($path, $pattern) === 0) {
                return true;
            }
        }

        return false;
    }
}
