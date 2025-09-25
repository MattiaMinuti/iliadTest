<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products with optional filtering and search.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'stock_status' => $request->get('stock_status'),
                'search' => $request->get('search'),
                'sort_by' => $request->get('sort_by', 'name'),
                'sort_order' => $request->get('sort_order', 'asc'),
            ];

            $perPage = $request->get('per_page', 15);
            $products = $this->productService->getProductsWithFilters($filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'sku' => 'required|string|max:255',
                'stock_quantity' => 'required|integer|min:0',
            ]);

            $product = $this->productService->createProduct($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product,
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
            ], 400);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->findByIdOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->findByIdOrFail($id);

            $this->validate($request, [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'sku' => 'sometimes|required|string|max:255',
                'stock_quantity' => 'sometimes|required|integer|min:0',
            ]);

            $product = $this->productService->updateProduct($product, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product,
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
                'message' => 'Product not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $product = $this->productService->findByIdOrFail($id);
            $this->productService->deleteProduct($product);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get out of stock products.
     */
    public function outOfStock(): JsonResponse
    {
        try {
            $products = $this->productService->getOutOfStockProducts();

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve out of stock products: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get low stock products.
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $threshold = $request->get('threshold', 10);
            $products = $this->productService->getLowStockProducts($threshold);

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve low stock products: ' . $e->getMessage(),
            ], 500);
        }
    }
}
