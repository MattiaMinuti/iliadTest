# Order Management API Documentation

## Base URL
```
http://localhost:8000/api/v1
```

## Authentication
Currently, the API does not require authentication. In a production environment, you would implement JWT or OAuth2 authentication.

## Response Format

All API responses follow a consistent format:

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data here
  },
  "message": "Optional success message"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    // Validation errors (if applicable)
  }
}
```

## Orders API

### GET /orders
Retrieve a paginated list of orders with optional filtering.

**Query Parameters:**
- `search` (string, optional): Search in order name and description
- `start_date` (date, optional): Filter orders from this date (YYYY-MM-DD)
- `end_date` (date, optional): Filter orders until this date (YYYY-MM-DD)
- `status` (string, optional): Filter by order status (pending, processing, completed, cancelled)
- `page` (integer, optional): Page number for pagination (default: 1)
- `per_page` (integer, optional): Items per page (default: 15, max: 100)
- `sort_by` (string, optional): Sort field (default: order_date)
- `sort_direction` (string, optional): Sort direction - asc or desc (default: desc)

**Example Request:**
```
GET /api/v1/orders?search=john&status=pending&page=1&per_page=10
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Office Setup - John Doe",
        "description": "Complete office setup for remote work",
        "total_amount": "1659.96",
        "status": "completed",
        "order_date": "2024-01-18",
        "created_at": "2024-01-23T10:00:00.000000Z",
        "updated_at": "2024-01-23T10:00:00.000000Z",
        "products": [
          {
            "id": 1,
            "name": "Laptop Pro 15\"",
            "description": "High-performance laptop with 16GB RAM and 512GB SSD",
            "price": "1299.99",
            "sku": "LAPTOP-PRO-15",
            "stock_quantity": 24,
            "pivot": {
              "quantity": 1,
              "unit_price": "1299.99",
              "total_price": "1299.99"
            }
          }
        ]
      }
    ],
    "current_page": 1,
    "per_page": 10,
    "total": 4,
    "last_page": 1
  }
}
```

### GET /orders/{id}
Retrieve a specific order by ID with its associated products.

**Path Parameters:**
- `id` (integer, required): Order ID

**Example Request:**
```
GET /api/v1/orders/1
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Office Setup - John Doe",
    "description": "Complete office setup for remote work",
    "total_amount": "1659.96",
    "status": "completed",
    "order_date": "2024-01-18",
    "created_at": "2024-01-23T10:00:00.000000Z",
    "updated_at": "2024-01-23T10:00:00.000000Z",
    "products": [...]
  }
}
```

### POST /orders
Create a new order with associated products.

**Request Body:**
```json
{
  "name": "New Order Name",
  "description": "Optional order description",
  "order_date": "2024-01-23",
  "status": "pending",
  "products": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 3,
      "quantity": 1
    }
  ]
}
```

**Validation Rules:**
- `name`: Required, string, max 255 characters
- `description`: Optional, string
- `order_date`: Required, valid date
- `status`: Optional, must be one of: pending, processing, completed, cancelled
- `products`: Required, array with at least 1 item
- `products.*.product_id`: Required, must exist in products table
- `products.*.quantity`: Required, integer, minimum 1

**Example Response:**
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 5,
    "name": "New Order Name",
    "description": "Optional order description",
    "total_amount": "2729.97",
    "status": "pending",
    "order_date": "2024-01-23",
    "created_at": "2024-01-23T12:00:00.000000Z",
    "updated_at": "2024-01-23T12:00:00.000000Z",
    "products": [...]
  }
}
```

### PUT /orders/{id}
Update an existing order.

**Path Parameters:**
- `id` (integer, required): Order ID

**Request Body:** (All fields optional for updates)
```json
{
  "name": "Updated Order Name",
  "description": "Updated description",
  "order_date": "2024-01-24",
  "status": "processing",
  "products": [
    {
      "product_id": 1,
      "quantity": 3
    }
  ]
}
```

**Example Response:**
```json
{
  "success": true,
  "message": "Order updated successfully",
  "data": {
    // Updated order data
  }
}
```

### DELETE /orders/{id}
Delete an order and restore stock for associated products.

**Path Parameters:**
- `id` (integer, required): Order ID

**Example Response:**
```json
{
  "success": true,
  "message": "Order deleted successfully"
}
```

## Products API

### GET /products
Retrieve a paginated list of products with optional filtering.

**Query Parameters:**
- `search` (string, optional): Search in product name, description, and SKU
- `in_stock` (boolean, optional): Filter by stock availability (true/false)
- `page` (integer, optional): Page number for pagination (default: 1)
- `per_page` (integer, optional): Items per page (default: 15, max: 100)
- `sort_by` (string, optional): Sort field (default: name)
- `sort_direction` (string, optional): Sort direction - asc or desc (default: asc)

**Example Request:**
```
GET /api/v1/products?search=laptop&in_stock=true&sort_by=price
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Laptop Pro 15\"",
        "description": "High-performance laptop with 16GB RAM and 512GB SSD",
        "price": "1299.99",
        "sku": "LAPTOP-PRO-15",
        "stock_quantity": 24,
        "created_at": "2024-01-23T10:00:00.000000Z",
        "updated_at": "2024-01-23T10:00:00.000000Z"
      }
    ],
    "current_page": 1,
    "per_page": 15,
    "total": 8,
    "last_page": 1
  }
}
```

### GET /products/{id}
Retrieve a specific product by ID.

**Path Parameters:**
- `id` (integer, required): Product ID

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Laptop Pro 15\"",
    "description": "High-performance laptop with 16GB RAM and 512GB SSD",
    "price": "1299.99",
    "sku": "LAPTOP-PRO-15",
    "stock_quantity": 24,
    "created_at": "2024-01-23T10:00:00.000000Z",
    "updated_at": "2024-01-23T10:00:00.000000Z"
  }
}
```

### POST /products
Create a new product.

**Request Body:**
```json
{
  "name": "New Product",
  "description": "Product description",
  "price": 99.99,
  "sku": "NEW-PRODUCT-001",
  "stock_quantity": 50
}
```

**Validation Rules:**
- `name`: Required, string, max 255 characters
- `description`: Optional, string
- `price`: Required, numeric, minimum 0
- `sku`: Required, string, must be unique
- `stock_quantity`: Required, integer, minimum 0

### PUT /products/{id}
Update an existing product.

**Path Parameters:**
- `id` (integer, required): Product ID

**Request Body:** (All fields optional for updates)
```json
{
  "name": "Updated Product Name",
  "price": 149.99,
  "stock_quantity": 75
}
```

### DELETE /products/{id}
Delete a product (only if not associated with any orders).

**Path Parameters:**
- `id` (integer, required): Product ID

**Example Response:**
```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

**Error Response (if product is used in orders):**
```json
{
  "success": false,
  "message": "Cannot delete product that is associated with orders"
}
```

## Error Codes

- **200 OK**: Request successful
- **201 Created**: Resource created successfully
- **400 Bad Request**: Invalid request data or business logic violation
- **404 Not Found**: Resource not found
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server error

## Stock Management

The API automatically manages product stock:

1. **Creating an Order**: Reduces stock quantity for each product
2. **Updating an Order**: Restores old stock, then reduces new stock
3. **Deleting an Order**: Restores stock for all products in the order
4. **Stock Validation**: Prevents orders if insufficient stock is available

## Pagination

All list endpoints support pagination with the following parameters:
- `page`: Current page number (starts from 1)
- `per_page`: Number of items per page (default: 15, max: 100)

Pagination information is included in the response:
```json
{
  "current_page": 1,
  "per_page": 15,
  "total": 100,
  "last_page": 7,
  "from": 1,
  "to": 15
}
```

## Rate Limiting

Currently, no rate limiting is implemented. In production, consider implementing rate limiting to prevent API abuse.

## CORS

The API is configured to accept requests from any origin (`*`). In production, configure specific allowed origins for security.

---

For more detailed information about the implementation, please refer to the main README.md file.
