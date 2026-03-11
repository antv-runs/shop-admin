<?php

namespace App\Constants;

/**
 * Centralized cache key management
 * All cache keys follow the pattern: resource:type:identifier:extra
 */
class CacheKey
{
    /**
     * Cache key for paginated products list
     * Format: products:list:{page}:{perPage}:{search}:{categoryId}:{status}
     */
    public static function productList(int $page, int $perPage, string $search, string $categoryId, string $status): string
    {
        return "products:list:{$page}:{$perPage}:{$search}:{$categoryId}:{$status}";
    }

    /**
     * Cache key for product detail
     * Format: products:detail:{id}
     */
    public static function productDetail(int $id): string
    {
        return "products:detail:{$id}";
    }

    /**
     * Cache key for paginated users list
     * Format: users:list:{page}:{perPage}:{search}:{status}:{role}:{sortBy}:{sortOrder}
     */
    public static function userList(int $page, int $perPage, string $search, string $status, string $role, string $sortBy, string $sortOrder): string
    {
        return "users:list:{$page}:{$perPage}:{$search}:{$status}:{$role}:{$sortBy}:{$sortOrder}";
    }

    /**
     * Cache key for user detail
     * Format: users:detail:{id}
     */
    public static function userDetail(int $id): string
    {
        return "users:detail:{$id}";
    }

    /**
     * Cache key for user orders list
     * Format: orders:list:user:{userId}:{perPage}
     */
    public static function userOrdersList(int $userId, int $perPage): string
    {
        return "orders:list:user:{$userId}:{$perPage}";
    }

    /**
     * Cache key for order detail
     * Format: orders:detail:{orderId}
     */
    public static function orderDetail(int $orderId): string
    {
        return "orders:detail:{$orderId}";
    }

    /**
     * Cache key for categories list
     * Format: categories:list:{page}:{perPage}:{search}:{status}
     */
    public static function categoryList(int $page, int $perPage, string $search, string $status): string
    {
        return "categories:list:{$page}:{$perPage}:{$search}:{$status}";
    }

    /**
     * Cache key for category detail
     * Format: categories:detail:{id}
     */
    public static function categoryDetail(int $id): string
    {
        return "categories:detail:{$id}";
    }
}
