<?php

namespace App\Services;

use App\Contracts\OrderServiceInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\DTOs\CreateOrderDTO;
use App\Helpers\CacheHelper;
use App\Constants\CacheKey;
use App\Constants\CacheConstants;

class OrderService implements OrderServiceInterface
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Create a new order and its items inside a transaction
     *
     * The $data array should contain:
     *  - user_id
     *     - items: array of ['product_id'=>int, 'quantity'=>int]
     *
     * Prices are looked up from the products table to prevent trusting FE data.
     */
    public function createOrder(CreateOrderDTO $dto)
    {
        $data = $dto->toArray();
        $userId = $data['user_id'];

        $result = $this->orderRepository->create($data);

        // Invalidate user's order list cache
        CacheHelper::forget(CacheKey::userOrdersList($userId, 15));

        return $result;
    }

    /**
     * Get orders belonging to a user (paginated)
     * Cached with TTL of 300 seconds
     */
    public function getOrdersForUser($userId, $perPage = 15)
    {
        $cacheKey = CacheKey::userOrdersList($userId, $perPage);

        return CacheHelper::remember($cacheKey, CacheConstants::CACHE_TTL, function () use ($userId, $perPage) {
            return $this->orderRepository->getOrdersForUser($userId, $perPage);
        });
    }

    /**
     * Retrieve a single order and ensure the given user owns it.
     * Cached with TTL of 300 seconds
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getOrderForUser($orderId, $userId)
    {
        $cacheKey = CacheKey::orderDetail($orderId);

        return CacheHelper::remember($cacheKey, CacheConstants::CACHE_TTL, function () use ($orderId, $userId) {
            return $this->orderRepository->getOrderForUser($orderId, $userId);
        });
    }
}
