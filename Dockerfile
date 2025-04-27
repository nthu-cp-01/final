# Use PHP 8.3 FPM Alpine as base
FROM php:8.3-fpm-alpine

# Install dependencies
RUN apk update && apk upgrade && apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
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
RUN composer install --no-dev --optimize-autoloader

# Install npm dependencies and build assets
RUN npm install && npm run build

# Configure nginx
COPY deployment/docker/nginx.conf /etc/nginx/http.d/default.conf

# Set up supervisor_killall script
COPY deployment/docker/supervisor_killall.sh /usr/local/bin/supervisor_killall
RUN chmod +x /usr/local/bin/supervisor_killall && mkdir -p /var/log/supervisor

# Configure supervisord
COPY deployment/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel optimization
RUN php artisan config:clear
RUN php artisan route:cache
RUN php artisan view:cache
RUN php artisan event:cache

# Expose port 80
EXPOSE 80

# Start supervisord
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
