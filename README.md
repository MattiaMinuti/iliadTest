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
- [TablePlus](https://tableplus.com/) (recommended for database management)

## 🔧 Initial Setup

After cloning the repository, you need to set up Git hooks for code quality:

```bash
# Set up Git hooks (required for code quality checks)
./setup-hooks.sh
```

This will install:
- **pre-commit**: Automatic PHP CS Fixer and Prettier formatting
- **pre-push**: Backend API tests before pushing to main branch

## 🚀 Quick Start with Docker

> **⚠️ Important**: Follow these steps in the exact order shown to avoid common setup issues. The backend dependencies must be installed before running database migrations.

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
- **Backend API** on port 8080
- **Frontend Application** on port 80 (accessible via http://localhost)

### 3. Install Backend Dependencies
```bash
# Install PHP dependencies in the backend container
docker-compose exec backend composer install
```

### 4. Initialize the Database
```bash
# Run migrations and seed the database with sample data
docker-compose exec backend bash -c "php artisan migrate:fresh --force && php artisan db:seed --force"
```

> **Note**: We use `migrate:fresh --force` to ensure a clean database setup and `--force` flag to bypass production environment warnings.

### 5. Access the Application

#### Option 1: Standard URLs (Recommended for Quick Start)
- **Frontend**: http://localhost
- **Backend API**: http://localhost:8080
- **API Documentation**: http://localhost:8080/swagger

#### Option 2: Custom Domain (Optional)
For a professional development experience, you can access the application using custom domains:

1. **Add to hosts file** (one-time setup):
   ```bash
   # On macOS/Linux (requires sudo password)
   echo "127.0.0.1 iliadlocal iliadApi" | sudo tee -a /etc/hosts
   
   # On Windows (run as Administrator)
   echo 127.0.0.1 iliadlocal iliadApi >> C:\Windows\System32\drivers\etc\hosts
   ```

2. **Access the application**:
   - **Frontend**: http://iliadlocal (clean URL without port)
   - **Backend API**: http://iliadApi:8000 (professional API domain)

> 📖 **Detailed Setup Guide**: See [Custom Domain Setup](docs/CUSTOM_DOMAIN_SETUP.md) for comprehensive instructions, troubleshooting, and advanced configuration options.

### 6. Troubleshooting

#### Common Issues and Solutions

**Issue**: `Failed to open stream: No such file or directory in /var/www/html/vendor/autoload.php`
```bash
# Solution: Install Composer dependencies first
docker-compose exec backend composer install
```

**Issue**: `Call to undefined method App\Models\Product::reduceStock()`
```bash
# Solution: This method is now included in the Product model
# If you encounter this error, make sure you have the latest code
git pull origin main
```

**Issue**: `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry`
```bash
# Solution: Use migrate:fresh to reset the database
docker-compose exec backend bash -c "php artisan migrate:fresh --force && php artisan db:seed --force"
```

**Issue**: `Do you really wish to run this command? (yes/no) [no]`
```bash
# Solution: Use --force flag for production environment
docker-compose exec backend bash -c "php artisan migrate --force"
```

### 7. Verify Setup

After completing the setup, verify that everything is working correctly:

```bash
# Check if all containers are running
docker-compose ps

# Test the API
curl http://localhost:8080/api/v1/products

# Test the frontend
curl http://localhost
```

**Expected Results:**
- All containers should show "Up" status
- API should return JSON with products data
- Frontend should return HTML content

### 8. Database Management
For database management, we recommend using **TablePlus** (modern, fast, and user-friendly):

**Connection Settings:**
- **Host**: localhost
- **Port**: 3306
- **Database**: order_management
- **Username**: app_user
- **Password**: app_password

**Alternative Tools:**
- MySQL Workbench
- Sequel Pro (macOS)
- DBeaver (cross-platform)

## 🗄️ Database Management with TablePlus

TablePlus is our recommended database management tool for its modern interface and excellent performance.

### Setting up TablePlus Connection

1. **Download and Install TablePlus**
   - Visit [tableplus.com](https://tableplus.com/)
   - Download the appropriate version for your OS
   - Install the application

2. **Create New Connection**
   - Open TablePlus
   - Click "Create a new connection"
   - Select "MySQL" from the database types

3. **Configure Connection Settings**
   ```
   Name: Gestionale Iliad
   Host: localhost
   Port: 3306
   User: app_user
   Password: app_password
   Database: order_management
   ```

4. **Test Connection**
   - Click "Test" to verify the connection
   - If successful, click "Connect"

### TablePlus Features for This Project

- **Visual Query Builder**: Easy to build complex queries
- **Data Export/Import**: Export data to CSV, JSON, SQL
- **Real-time Data**: See changes immediately
- **Multiple Tabs**: Work with multiple tables simultaneously
- **SQL Editor**: Advanced SQL editing with syntax highlighting
- **Data Visualization**: Charts and graphs for data analysis
- 
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
   php artisan migrate:fresh --force
   php artisan db:seed --force
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