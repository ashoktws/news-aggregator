# Set the base image to use
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    zip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required for Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql xml

# Set the working directory inside the container
WORKDIR /var/www

# Copy composer.lock and composer.json
COPY composer.json composer.lock ./

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP dependencies via Composer
RUN composer install --no-autoloader --no-scripts

# Copy the rest of the application code
COPY . .

# Run composer autoloader and optimize
RUN composer dump-autoload --optimize

# Expose the port the app will run on
EXPOSE 9000

# Start the PHP-FPM server
CMD ["php-fpm"]
