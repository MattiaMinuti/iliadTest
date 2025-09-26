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

### 5. Database Management
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