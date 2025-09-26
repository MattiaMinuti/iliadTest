<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected $orderService;

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
            $filters = [
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'status' => $request->get('status'),
                'search' => $request->get('search'),
                'sort_by' => $request->get('sort_by', 'order_date'),
                'sort_order' => $request->get('sort_direction', 'desc'),
            ];

            $perPage = $request->get('per_page', 15);
            $orders = $this->orderService->getOrdersWithFilters($filters, $perPage);

            return $this->paginatedResponse($orders, 'Orders retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve orders: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'order_date' => 'required|date',
                'status' => 'in:pending,processing,completed,cancelled',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|integer|min:1',
            ]);

            $orderData = [
                'name' => $request->name,
                'description' => $request->description,
                'order_date' => $request->order_date,
                'status' => $request->get('status', 'pending'),
                'products' => $request->products,
            ];

            $order = $this->orderService->createOrder($orderData);

            return $this->createdResponse($order, 'Order created successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve order: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->findByIdOrFail($id);

            $this->validate($request, [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'order_date' => 'sometimes|required|date',
                'status' => 'sometimes|in:pending,processing,completed,cancelled',
                'products' => 'sometimes|array|min:1',
                'products.*.product_id' => 'required_with:products|exists:products,id',
                'products.*.quantity' => 'required_with:products|integer|min:1',
            ]);

            $orderData = $request->only(['name', 'description', 'order_date', 'status']);

            if ($request->has('products')) {
                $orderData['products'] = $request->products;
            }

            $order = $this->orderService->updateOrder($order, $orderData);

            return $this->updatedResponse($order, 'Order updated successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Order not found');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified order.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->findByIdOrFail($id);
            $this->orderService->deleteOrder($order);

            return $this->deletedResponse('Order deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Order not found');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete order: ' . $e->getMessage());
        }
    }
}
