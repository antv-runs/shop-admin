<?php

namespace App\Repositories;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Create a new order
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $userId = $data['user_id'];
            $items = $data['items'] ?? [];

            if (empty($items)) {
                throw new \InvalidArgumentException('Order must contain at least one item.');
            }

            $totalAmount = 0;
            $orderItemsPayload = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = max(1, (int) $item['quantity']);
                $price = $product->price;
                $lineTotal = bcmul($price, $quantity, 2);

                $totalAmount = bcadd($totalAmount, $lineTotal, 2);

                $orderItemsPayload[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $lineTotal,
                ];
            }

            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => $totalAmount,
            ]);

            // create items using relationship for convenience
            foreach ($orderItemsPayload as $payload) {
                $order->items()->create($payload);
            }

            // load items relationship before returning
            $order->load('items.product');

            return $order;
        });
    }

    /**
     * Find an order by ID
     */
    public function findById($id)
    {
        return Order::findOrFail($id);
    }

    /**
     * Get orders for a user (paginated)
     */
    public function getOrdersForUser($userId, $perPage = 15)
    {
        return Order::with('items.product')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get a specific order for a user
     */
    public function getOrderForUser($orderId, $userId)
    {
        return Order::with('items.product')
            ->where('id', $orderId)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    /**
     * Paginate orders
     */
    public function paginate($perPage = 15)
    {
        return Order::paginate($perPage);
    }
}
