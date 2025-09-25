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

            return response()->json([
                'success' => true,
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders: ' . $e->getMessage(),
            ], 500);
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

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
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
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order: ' . $e->getMessage(),
            ], 500);
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

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'data' => $order,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order',
                'error' => $e->getMessage(),
            ], 500);
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

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
