# Use PHP 8.3 FPM Alpine as base
FROM php:8.3-fpm-alpine

# Install dependencies
RUN apk update && apk upgrade && apk add --no-cache \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    postgresql-dev \
    pcre-dev \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install \
      pdo \
      pdo_pgsql \
      mbstring \
      exif \
      pcntl \
      bcmath \
      gd \
      zip \
      intl \
      xml

# Install pecl dependencies
RUN pecl update-channels \
    && pecl install redis \
    && docker-php-ext-enable redis

# Purge phpize dependencies
RUN apk del $PHPIZE_DEPS

# Set working directory
WORKDIR /var/www/html

# Copy composer files first to leverage Docker cache
COPY composer.json composer.lock ./

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy project files
COPY . .

# Install composer dependencies
RUN composer install

# Laravel optimization
RUN php artisan config:clear

# Start supervisord
CMD ["php", "artisan", "migrate:fresh", "--seed", "--force"]
