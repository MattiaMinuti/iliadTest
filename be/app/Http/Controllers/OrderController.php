<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with optional filtering and search.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with('products');

        // Apply date range filter
        if ($request->has('start_date') || $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Apply search filter
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Apply status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'order_date');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
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

            // FIRST: Check stock availability for ALL products before creating order
            $productsToOrder = [];
            $totalAmount = 0;
            
            foreach ($request->products as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                
                // Check stock availability
                if (!$product->hasStock($productData['quantity'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$product->name}",
                    ], 400);
                }

                $unitPrice = $product->price;
                $totalPrice = $unitPrice * $productData['quantity'];
                $totalAmount += $totalPrice;
                
                $productsToOrder[] = [
                    'product' => $product,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            // SECOND: Create order only if all products are available
            $order = Order::create([
                'name' => $request->name,
                'description' => $request->description,
                'order_date' => $request->order_date,
                'status' => $request->get('status', 'pending'),
                'total_amount' => $totalAmount,
            ]);

            // THIRD: Attach products and reduce stock
            foreach ($productsToOrder as $productOrder) {
                $order->products()->attach($productOrder['product']->id, [
                    'quantity' => $productOrder['quantity'],
                    'unit_price' => $productOrder['unit_price'],
                    'total_price' => $productOrder['total_price'],
                ]);

                // Reduce stock
                $productOrder['product']->reduceStock($productOrder['quantity']);
            }
            $order->load('products');

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
                'message' => 'Failed to create order',
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
            $order = Order::with('products')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            $this->validate($request, [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'order_date' => 'sometimes|required|date',
                'status' => 'sometimes|in:pending,processing,completed,cancelled',
                'products' => 'sometimes|array|min:1',
                'products.*.product_id' => 'required_with:products|exists:products,id',
                'products.*.quantity' => 'required_with:products|integer|min:1',
            ]);

            // Update basic order information
            $order->fill($request->only(['name', 'description', 'order_date', 'status']));

            // Update products if provided
            if ($request->has('products')) {
                // FIRST: Check stock availability for ALL new products before making changes
                $productsToOrder = [];
                $totalAmount = 0;
                
                foreach ($request->products as $productData) {
                    $product = Product::findOrFail($productData['product_id']);
                    
                    // Calculate available stock (current stock + what we'll restore from this order)
                    $currentOrderQuantity = 0;
                    $currentProduct = $order->products->where('id', $product->id)->first();
                    if ($currentProduct) {
                        $currentOrderQuantity = $currentProduct->pivot->quantity;
                    }
                    
                    $availableStock = $product->stock_quantity + $currentOrderQuantity;
                    
                    // Check if we have enough stock
                    if ($availableStock < $productData['quantity']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for product: {$product->name}",
                        ], 400);
                    }

                    $unitPrice = $product->price;
                    $totalPrice = $unitPrice * $productData['quantity'];
                    $totalAmount += $totalPrice;
                    
                    $productsToOrder[] = [
                        'product' => $product,
                        'quantity' => $productData['quantity'],
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ];
                }

                // SECOND: Restore stock from current products (now that we know we can fulfill the new order)
                foreach ($order->products as $currentProduct) {
                    $currentProduct->increaseStock($currentProduct->pivot->quantity);
                }

                // THIRD: Clear current products
                $order->products()->detach();

                // FOURTH: Attach new products and reduce stock
                foreach ($productsToOrder as $productOrder) {
                    $order->products()->attach($productOrder['product']->id, [
                        'quantity' => $productOrder['quantity'],
                        'unit_price' => $productOrder['unit_price'],
                        'total_price' => $productOrder['total_price'],
                    ]);

                    // Reduce stock
                    $productOrder['product']->reduceStock($productOrder['quantity']);
                }

                $order->total_amount = $totalAmount;
            }

            $order->save();
            $order->load('products');

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
            $order = Order::findOrFail($id);

            // Restore stock for all products in the order
            foreach ($order->products as $product) {
                $product->increaseStock($product->pivot->quantity);
            }

            $order->delete();

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
                'message' => 'Failed to delete order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
