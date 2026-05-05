FROM php:8.3-apache

# 1. Install dependencies sistem (tambah libicu-dev & libzip-dev)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libicu-dev \
    libzip-dev

# 2. Enable Apache modules
RUN a2enmod rewrite

# 3. Install & Enable PHP extensions (tambahkan intl dan zip)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# 4. Copy application
COPY . /var/www/html
WORKDIR /var/www/html

# 5. Tambahkan exception untuk Git (agar tidak error 'dubious ownership')
RUN git config --global --add safe.directory /var/www/html

# 6. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 7. Set permissions agar folder storage bisa ditulisi
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Konfigurasi Port untuk Cloud Run (wajib 8080)
EXPOSE 8080
ENV PORT 8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

CMD ["apache2-foreground"]