# Use PHP 8.2 with Apache
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY . .

# Create storage directories and set permissions
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} \
    && chown -R www-data:www-data storage \
    && chmod -R 775 storage

# Copy environment file
COPY env.example .env

# Generate application key (if needed)
# RUN php artisan key:generate

# Expose port 8000
EXPOSE 8000

# Start the application
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
