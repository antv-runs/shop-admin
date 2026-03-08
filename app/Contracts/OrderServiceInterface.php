<?php

namespace App\Contracts;

use App\DTOs\CreateOrderDTO;

interface OrderServiceInterface
{
    /**
     * Create a new order
     *
     * @param CreateOrderDTO $dto
     * @return \App\Models\Order
     */
    public function createOrder(CreateOrderDTO $dto);

    /**
     * Get paginated orders belonging to a user
     */
    public function getOrdersForUser($userId, $perPage = 15);

    /**
     * Get a single order by id ensuring it belongs to given user
     */
    public function getOrderForUser($orderId, $userId);
}
