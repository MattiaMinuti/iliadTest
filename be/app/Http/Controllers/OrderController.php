<?php

namespace App\Http\Controllers;

use App\Http\Traits\OrderValidation;
use App\Models\Order;
use App\Services\OrderService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use OrderValidation;

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders with optional filtering and search.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->validateOrderFilter($request);

            $filters = [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'status' => $request->input('status'),
                'search' => $request->input('search'),
                'sort_by' => $request->input('sort_by', 'order_date'),
                'sort_order' => $request->input('sort_direction', 'desc'),
            ];

            $perPage = $request->get('per_page', 15);
            $orders = $this->orderService->getOrdersWithFilters($filters, $perPage);

            return $this->paginatedResponse($orders, 'Orders retrieved successfully');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve orders: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->validateOrderCreate($request);

            $orderData = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'order_date' => $request->input('order_date'),
                'status' => $request->input('status', 'pending'),
                'products' => $request->input('products'),
            ];

            $order = $this->orderService->createOrder($orderData);

            return $this->createdResponse($order, 'Order created successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified order.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderWithProducts($id);

            if (! $order) {
                return $this->notFoundResponse('Order not found');
            }

            return $this->successResponse($order, 'Order retrieved successfully');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve order: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            /** @var Order $order */
            $order = $this->orderService->findByIdOrFail($id);

            $this->validateOrderUpdate($request);

            $orderData = $request->only(['name', 'description', 'order_date', 'status']);

            if ($request->has('products')) {
                $orderData['products'] = $request->input('products');
            }

            $order = $this->orderService->updateOrder($order, $orderData);

            return $this->updatedResponse($order, 'Order updated successfully');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Order not found');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified order.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            /** @var Order $order */
            $order = $this->orderService->findByIdOrFail($id);
            $this->orderService->deleteOrder($order);

            return $this->deletedResponse('Order deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Order not found');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Failed to delete order: ' . $e->getMessage());
        }
    }
}
