<?php

namespace App\Contracts\Repositories;

interface OrderRepositoryInterface
{
    /**
     * Create a new order
     */
    public function create(array $data);

    /**
     * Find an order by ID
     */
    public function findById($id);

    /**
     * Get orders for a user (paginated)
     */
    public function getOrdersForUser($userId, $perPage = 15);

    /**
     * Get a specific order for a user
     */
    public function getOrderForUser($orderId, $userId);

    /**
     * Paginate orders
     */
    public function paginate($perPage = 15);
}
