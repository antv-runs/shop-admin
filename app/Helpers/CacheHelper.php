<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheHelper
{
    /**
     * Remember data with cache hit/miss logging
     *
     * @param string $key
     * @param int $ttl
     * @param \Closure $callback
     * @return mixed
     */
    public static function remember(string $key, int $ttl, \Closure $callback)
    {
        // Check if key exists in cache before using remember
        if (Cache::has($key)) {
            Log::channel('single')->info("Cache hit: {$key}");
            return Cache::get($key);
        }

        Log::channel('single')->info("Cache miss: {$key}");

        // Use Laravel's remember to cache the result
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Forget cache key with logging
     *
     * @param string $key
     * @return void
     */
    public static function forget(string $key): void
    {
        Cache::forget($key);
        Log::channel('single')->info("Cache invalidated: {$key}");
    }

    /**
     * Flush cache tags with logging
     *
     * @param array $tags
     * @return void
     */
    public static function flushTags(array $tags): void
    {
        foreach ($tags as $tag) {
            Cache::tags([$tag])->flush();
            Log::channel('single')->info("Cache flushed: {$tag}");
        }
    }
}
