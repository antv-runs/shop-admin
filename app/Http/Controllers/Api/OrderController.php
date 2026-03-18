<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Jobs\SendOrderCreatedEmail;
use App\DTOs\CreateOrderDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends BaseController
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="List orders belonging to authenticated user",
        *     description="Retrieve a paginated list of orders for the authenticated user.",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", description="Page number (default: 1)", @OA\Schema(type="integer", minimum=1)),
        *     @OA\Parameter(name="per_page", in="query", description="Items per page (default: 15)", @OA\Schema(type="integer", minimum=1, default=15)),
     *     @OA\Response(
     *         response=200,
     *         description="Orders retrieved successfully",
     *         @OA\JsonContent(
        *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
        *             @OA\Property(property="links", type="object",
        *                 @OA\Property(property="first", type="string"),
        *                 @OA\Property(property="last", type="string"),
        *                 @OA\Property(property="prev", type="string", nullable=true),
        *                 @OA\Property(property="next", type="string", nullable=true)
        *             ),
        *             @OA\Property(property="meta", type="object",
        *                 @OA\Property(property="current_page", type="integer"),
        *                 @OA\Property(property="last_page", type="integer"),
        *                 @OA\Property(property="per_page", type="integer"),
        *                 @OA\Property(property="total", type="integer")
        *             ),
     *             @OA\Property(property="success", type="boolean", example=true),
        *             @OA\Property(property="message", type="string", example="Orders retrieved successfully")
     *         )
     *     ),
        *     @OA\Response(
        *         response=401,
        *         description="Unauthenticated",
        *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
        *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $orders = $this->orderService->getOrdersForUser(auth()->id(), $perPage);
        return $this->success(OrderResource::collection($orders), 'Orders retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get order details",
     *     description="Retrieve details of a specific order that belongs to the authenticated user.",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Order ID", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Order details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found or does not belong to user",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show($id)
    {
        $order = $this->orderService->getOrderForUser($id, auth()->id());
        return $this->success(new OrderResource($order), 'Order retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     description="Create a new order for guest or authenticated users with customer and items payload. After creation, a queued SendOrderCreatedEmail job is dispatched after transaction commit.",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"customer", "items"},
     *             @OA\Property(
     *                 property="customer",
     *                 type="object",
     *                 required={"name", "email", "phone", "address"},
     *                 @OA\Property(property="name", type="string", maxLength=255, example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", maxLength=255, example="john@email.com"),
     *                 @OA\Property(property="phone", type="string", maxLength=50, example="0123456789"),
     *                 @OA\Property(property="address", type="string", maxLength=1000, example="Ho Chi Minh City")
     *             ),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 minItems=1,
     *                 example={{"product_id": 1, "quantity": 2, "color": "black", "size": "M"}},
     *                 @OA\Items(
     *                     required={"product_id", "quantity"},
     *                     @OA\Property(property="product_id", type="integer", description="Valid product ID that exists in database"),
     *                     @OA\Property(property="quantity", type="integer", minimum=1, description="Quantity of item ordered"),
     *                     @OA\Property(property="color", type="string", maxLength=50, nullable=true),
     *                     @OA\Property(property="size", type="string", maxLength=50, nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Order created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(OrderRequest $request)
    {
        $data = $request->validated();
        $customer = $data['customer'];

        $payload = [
            'name' => $customer['name'],
            'email' => $customer['email'],
            'phone' => $customer['phone'],
            'address' => $customer['address'],
            'items' => $data['items'],
        ];
        $payload['user_id'] = auth('sanctum')->id();

        $dto = CreateOrderDTO::fromArray($payload);
        $order = $this->orderService->createOrder($dto);

        // dispatch a queued job to send confirmation email after the transaction commits
        SendOrderCreatedEmail::dispatch($order)->afterCommit();

        return $this->success(new OrderResource($order), 'Order created successfully', Response::HTTP_CREATED);
    }
}
