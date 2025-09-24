# Gestionale Iliad

Un'applicazione web completa per la gestione degli ordini quotidiani degli utenti, sviluppata con backend PHP Lumen e frontend Vue.js con componenti Vuetify.

## 🚀 Features

### Backend (PHP Lumen)
- **RESTful API** with comprehensive CRUD operations
- **Gestione Ordini** con filtri, ricerca e ordinamento
- **Product Inventory** management
- **Database Relations** between orders and products
- **Stock Management** with automatic inventory updates
- **Data Validation** and error handling
- **API Testing** with PHPUnit

### Frontend (Vue.js + Vuetify)
- **Responsive Design** that works on all devices
- **Orders Dashboard** with advanced filtering and search
- **Detailed Order View** with associated products
- **Gestione Ordini** - Crea, modifica ed elimina ordini
- **Product Management** - Manage product inventory
- **Modern UI** with Material Design components
- **Real-time Notifications** for user feedback
- **Dark/Light Theme** support

### Key Functionalities
1. **Orders Viewing Page** with filters for date and search by name/description
2. **Detailed Order View** showing associated products and order information
3. **Order CRUD Operations** - Create, edit, and delete orders
4. **Product Management** - Full product lifecycle management
5. **Stock Management** - Automatic stock updates when orders are processed
6. **Responsive Design** - Works seamlessly on desktop, tablet, and mobile

## 🛠️ Tech Stack

- **Backend**: PHP 8.2, Lumen Framework
- **Frontend**: Vue.js 3, Vuetify 3 (Material UI)
- **Database**: MySQL 8.0
- **Development**: Docker, Docker Compose
- **Testing**: PHPUnit for backend, Vitest for frontend
- **Build Tools**: Vite for frontend bundling

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- [Docker](https://www.docker.com/get-started) (version 20.10+)
- [Docker Compose](https://docs.docker.com/compose/install/) (version 2.0+)
- [Git](https://git-scm.com/)

## 🚀 Quick Start with Docker

### 1. Clone the Repository
```bash
git clone <repository-url>
cd iliad
```

### 2. Start with Docker Compose
```bash
# Build and start all services
docker-compose up -d --build

# View logs (optional)
docker-compose logs -f
```

This will start:
- **MySQL Database** on port 3306
- **Backend API** on port 8000
- **Frontend Application** on port 3000
- **phpMyAdmin** on port 8080 (optional database management)

### 3. Initialize the Database
```bash
# Access the backend container
docker-compose exec backend bash

# Run migrations
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### 4. Access the Application
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080 (username: app_user, password: app_password)

## 🔧 Manual Setup (Alternative)

If you prefer to run the application without Docker:

### Backend Setup

1. **Navigate to backend directory**
   ```bash
   cd be
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp env.example .env
   # Edit .env file with your database credentials
   ```

4. **Set up database**
   ```bash
   # Create MySQL database named 'order_management'
   php artisan migrate
   php artisan db:seed
   ```

5. **Start the backend server**
   ```bash
   php -S localhost:8000 -t public
   ```

### Frontend Setup

1. **Navigate to frontend directory**
   ```bash
   cd fe
   ```

2. **Install Node.js dependencies**
   ```bash
   npm install
   ```

3. **Configure environment**
   ```bash
   cp env.example .env
   # Adjust VITE_API_URL if needed
   ```

4. **Start the development server**
   ```bash
   npm run dev
   ```

## 📊 Database Schema

### Orders Table
- `id` - Primary key
- `name` - Order name
- `description` - Order description (optional)
- `total_amount` - Calculated total amount
- `status` - Order status (pending, processing, completed, cancelled)
- `order_date` - Date of the order
- `created_at`, `updated_at` - Timestamps

### Products Table
- `id` - Primary key
- `name` - Product name
- `description` - Product description (optional)
- `price` - Product price
- `sku` - Stock Keeping Unit (unique)
- `stock_quantity` - Available stock quantity
- `created_at`, `updated_at` - Timestamps

### Order_Products Table (Pivot)
- `id` - Primary key
- `order_id` - Foreign key to orders
- `product_id` - Foreign key to products
- `quantity` - Quantity ordered
- `unit_price` - Price at time of order
- `total_price` - Calculated total for this line item
- `created_at`, `updated_at` - Timestamps

## 🔌 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Orders Endpoints

#### GET /orders
Get all orders with optional filtering
- **Query Parameters**:
  - `search` - Search in name and description
  - `start_date` - Filter by start date (YYYY-MM-DD)
  - `end_date` - Filter by end date (YYYY-MM-DD)
  - `status` - Filter by status
  - `page` - Page number for pagination
  - `per_page` - Items per page (default: 15)
  - `sort_by` - Sort field (default: order_date)
  - `sort_direction` - Sort direction (asc/desc, default: desc)

#### GET /orders/{id}
Get a specific order with products

#### POST /orders
Create a new order
```json
{
  "name": "Order Name",
  "description": "Order description",
  "order_date": "2024-01-01",
  "status": "pending",
  "products": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ]
}
```

#### PUT /orders/{id}
Update an existing order

#### DELETE /orders/{id}
Delete an order (restores stock)

### Products Endpoints

#### GET /products
Get all products with optional filtering
- **Query Parameters**:
  - `search` - Search in name, description, and SKU
  - `in_stock` - Filter by stock availability (true/false)
  - `page` - Page number for pagination
  - `per_page` - Items per page (default: 15)

#### GET /products/{id}
Get a specific product

#### POST /products
Create a new product
```json
{
  "name": "Product Name",
  "description": "Product description",
  "price": 99.99,
  "sku": "PRODUCT-001",
  "stock_quantity": 100
}
```

#### PUT /products/{id}
Update an existing product

#### DELETE /products/{id}
Delete a product (only if not used in orders)

## 🧪 Testing

### Backend Tests
```bash
# Run in backend container or directory
cd be
./vendor/bin/phpunit
```

### Frontend Tests
```bash
# Run in frontend directory
cd fe
npm run test
```

## 🎨 Frontend Features

### Responsive Design
- Mobile-first approach
- Tablet and desktop optimizations
- Touch-friendly interface

### Advanced Filtering
- **Orders**: Filter by date range, status, and search text
- **Products**: Filter by stock status and search text
- Real-time search with debouncing

### User Experience
- Loading states and progress indicators
- Toast notifications for user feedback
- Confirmation dialogs for destructive actions
- Intuitive navigation and breadcrumbs

### Accessibility
- ARIA labels and roles
- Keyboard navigation support
- High contrast mode compatibility
- Screen reader friendly

## 🔒 Security Features

- Input validation on both frontend and backend
- SQL injection prevention with Eloquent ORM
- XSS protection with proper data sanitization
- CORS configuration for API access
- Error handling without exposing sensitive information

## 📈 Performance Optimizations

### Backend
- Database indexing on frequently queried fields
- Eager loading of relationships
- Pagination for large datasets
- Optimized SQL queries

### Frontend
- Component lazy loading
- Debounced search inputs
- Efficient state management with Pinia
- Optimized bundle size with Vite

## 🚀 Production Deployment

### Docker Production Build
```bash
# Build production images
docker-compose -f docker-compose.prod.yml up -d --build
```

### Environment Configuration
1. Update environment variables for production
2. Set `APP_ENV=production` and `APP_DEBUG=false`
3. Configure proper database credentials
4. Set up SSL certificates for HTTPS
5. Configure proper CORS origins

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 API Response Format

All API responses follow this consistent format:

### Success Response
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Operation completed successfully"
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

### Paginated Response
```json
{
  "success": true,
  "data": {
    "data": [...],
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

## 📞 Support

For support and questions, please open an issue in the repository or contact the development team.

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

**Built with ❤️ for efficient order management**
