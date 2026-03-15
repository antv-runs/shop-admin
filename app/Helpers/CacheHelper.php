<?php

namespace App\Helpers;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheHelper
{
    /**
     * Check if the current cache driver supports tagging.
     */
    private static function supportsTagging(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }

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
        $value = Cache::get($key);

        // Check if key exists in cache before using remember
        if ($value !== null) {
            Log::info("Cache hit: {$key}");
            return $value;
        }

        Log::info("Cache miss: {$key}");

        $value = $callback();
        Cache::put($key, $value, $ttl);

        return $value;
    }

    /**
     * Remember data with cache hit/miss logging and tags
     *
     * @param array $tags
     * @param string $key
     * @param int $ttl
     * @param \Closure $callback
     * @return mixed
     */
    public static function rememberWithTags(array $tags, string $key, int $ttl, \Closure $callback)
    {
        if (!self::supportsTagging()) {
            Log::warning("Cache driver does not support tagging, falling back to non-tagged cache.", ['tags' => $tags, 'key' => $key]);
            return static::remember($key, $ttl, $callback);
        }

        $taggedCache = Cache::tags($tags);
        $value = $taggedCache->get($key);

        if ($value !== null) {
            Log::info("Cache hit: {$key}", ['tags' => $tags]);
            return $value;
        }

        Log::info("Cache miss: {$key}", ['tags' => $tags]);

        $value = $callback();
        $taggedCache->put($key, $value, $ttl);

        return $value;
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
        Log::info("Cache invalidated: {$key}");
    }

    /**
     * Flush cache tags with logging
     *
     * @param array $tags
     * @return void
     */
    public static function flushTags(array $tags): void
    {
        if (!self::supportsTagging()) {
            Log::warning("Cache driver does not support tagging, skipping tag flush.", ['tags' => $tags]);
            return;
        }

        foreach ($tags as $tag) {
            Cache::tags([$tag])->flush();
            Log::info("Cache flushed: {$tag}");
        }
    }
}
