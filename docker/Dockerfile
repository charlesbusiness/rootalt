# Use the official PHP image with PHP 8.3 as the base image
FROM php:8.3-fpm as app

USER root

# Install required dependencies and PHP extensions
RUN apt-get update && \
    apt-get install -y \
        unzip \
        curl \
        libzip-dev \
        libpng-dev \
        libssl-dev \
        libpq-dev \
        supervisor \
        && pecl install -o -f redis \
        && docker-php-ext-enable redis \
        && docker-php-ext-install gd zip pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Increase Composer timeout
RUN composer config --global process-timeout 2000

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the application code (including storage and bootstrap/cache folders)
COPY . /var/www/html

# Ensure storage and cache directories exist and set permissions
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM by default
CMD ["php-fpm"]
