<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Services\OrderService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create a new order
     *
     * @param OrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createOrder(OrderRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $order = $this->orderService->storeOrder($validatedData);

            $order->load(['orderItems', 'customer', 'branch', 'salesperson']);

            return $this->successResponse(
                ['order' => $order],
                'Order created successfully',
                Response::HTTP_CREATED
            );
        } catch (\Throwable $th) {
            Log::error('Order creation error: ' . $th->getMessage());
            return $this->errorResponse(
                'Failed to create order. Please try again.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get all orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->get('limit', 20);
            $filters = $request->only([
                'status',
                'payment_status',
                'customer_id',
                'branch_id',
                'created_at_start',
                'created_at_end'
            ]);

            $relations = ['orderItems', 'customer', 'branch', 'salesperson'];

            $orders = $this->orderService->getAllOrders($limit, $filters, $relations);

            return $this->successResponse(
                ['orders' => $orders],
                'Orders retrieved successfully'
            );
        } catch (\Throwable $th) {
            Log::error('Orders retrieval error: ' . $th->getMessage());
            return $this->errorResponse(
                'Failed to retrieve orders',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get a specific order
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $relations = ['orderItems.product', 'customer', 'branch', 'salesperson'];
            $order = $this->orderService->findOrder($relations, $id);

            if (!$order) {
                return $this->errorResponse(
                    'Order not found',
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->successResponse(
                ['order' => $order],
                'Order retrieved successfully'
            );
        } catch (\Throwable $th) {
            Log::error('Order retrieval error: ' . $th->getMessage());
            return $this->errorResponse(
                'Failed to retrieve order',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update order status
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, string $id)
    {
        try {
            $request->validate([
                'status' => 'required|integer'
            ]);

            $updated = $this->orderService->updateOrderStatus($id, $request->status);

            if (!$updated) {
                return $this->errorResponse(
                    'Order not found or failed to update',
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->successResponse(
                [],
                'Order status updated successfully'
            );
        } catch (\Throwable $th) {
            Log::error('Order status update error: ' . $th->getMessage());
            return $this->errorResponse(
                'Failed to update order status',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
