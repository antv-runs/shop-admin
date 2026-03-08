<?php

namespace App\Services;

use App\Contracts\OrderServiceInterface;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\DTOs\CreateOrderDTO;

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
        return $this->orderRepository->create($dto->toArray());
    }

    /**
     * Get orders belonging to a user (paginated)
     */
    public function getOrdersForUser($userId, $perPage = 15)
    {
        return $this->orderRepository->getOrdersForUser($userId, $perPage);
    }

    /**
     * Retrieve a single order and ensure the given user owns it.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getOrderForUser($orderId, $userId)
    {
        return $this->orderRepository->getOrderForUser($orderId, $userId);
    }
}
