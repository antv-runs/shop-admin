<?php

namespace App\Constants;

/**
 * Cache-related constants
 */
class CacheConstants
{
    /**
     * Default cache TTL in seconds (5 minutes)
     */
    public const CACHE_TTL = 300;

    public const TAG_PRODUCT_LIST = 'products:list';
    public const TAG_CATEGORY_LIST = 'categories:list';
}
