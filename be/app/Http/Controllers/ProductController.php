<?php

namespace App\Http\Controllers;

use App\Http\Traits\ProductValidation;
use App\Models\Product;
use App\Services\ProductService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use ProductValidation;

    protected ProductService $productService;

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
            $this->validateProductFilter($request);

            $filters = [
                'stock_status' => $request->get('stock_status'),
                'search' => $request->get('search'),
                'sort_by' => $request->get('sort_by', 'name'),
                'sort_order' => $request->get('sort_order', 'asc'),
            ];

            $perPage = $request->get('per_page', 15);
            $products = $this->productService->getProductsWithFilters($filters, $perPage);

            return $this->paginatedResponse($products, 'Products retrieved successfully');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve products: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->validateProductCreate($request);

            $product = $this->productService->createProduct($request->all());

            return $this->createdResponse($product, 'Product created successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->findByIdOrFail($id);

            return $this->successResponse($product, 'Product retrieved successfully');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Product not found');
        } catch (Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve product: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            /** @var Product $product */
            $product = $this->productService->findByIdOrFail($id);

            $this->validateProductUpdate($request, $id);

            $product = $this->productService->updateProduct($product, $request->all());

            return $this->updatedResponse($product, 'Product updated successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Product not found');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            /** @var Product $product */
            $product = $this->productService->findByIdOrFail($id);
            $this->productService->deleteProduct($product);

            return $this->deletedResponse('Product deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->notFoundResponse('Product not found');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
