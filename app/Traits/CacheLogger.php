<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait CacheLogger
{
    /**
     * Remember data with cache hit/miss logging
     *
     * @param string $key
     * @param int $ttl
     * @param \Closure $callback
     * @return mixed
     */
    protected function rememberWithLogging(string $key, int $ttl, \Closure $callback)
    {
        // Check if key exists in cache
        if (Cache::has($key)) {
            Log::info("Cache hit: {$key}");
            return Cache::get($key);
        }

        Log::info("Cache miss: {$key}");

        // Execute callback and cache result
        $result = $callback();
        Cache::put($key, $result, $ttl);

        return $result;
    }

    /**
     * Log cache forget operation
     *
     * @param string $key
     * @return void
     */
    protected function logCacheForget(string $key): void
    {
        Log::info("Cache invalidated: {$key}");
    }

    /**
     * Log cache flush operation with tag
     *
     * @param string $tag
     * @return void
     */
    protected function logCacheFlush(string $tag): void
    {
        Log::info("Cache flushed: {$tag}");
    }
}
