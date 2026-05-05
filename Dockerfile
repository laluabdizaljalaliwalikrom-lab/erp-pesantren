FROM php:8.3-apache

# 1. Install dependencies sistem
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

# 3. Ubah DocumentRoot Apache ke folder /public & Izinkan .htaccess
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN echo "<Directory ${APACHE_DOCUMENT_ROOT}>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    </Directory>" >> /etc/apache2/apache2.conf

# 4. Install & Enable PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# 5. Copy application
COPY . /var/www/html
WORKDIR /var/www/html

# 6. Tambahkan exception untuk Git
RUN git config --global --add safe.directory /var/www/html

# 7. Install Composer & Dependencies (PHP)
# Vendor harus ada sebelum npm run build karena CSS Filament mengimpor file dari vendor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 8. Install Node.js & NPM
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# 9. Install Node dependencies & build assets
RUN npm install
RUN npm run build

# 10. Set permissions (Pastikan www-data punya akses)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Konfigurasi Port untuk Cloud Run
EXPOSE 8080
ENV PORT 8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

CMD ["apache2-foreground"]