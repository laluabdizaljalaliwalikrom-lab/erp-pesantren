# MEMANGGIL BASE IMAGE (Sangat Cepat!)
# Pastikan Anda sudah menjalankan build base image di Cloud Shell terlebih dahulu
FROM asia-southeast2-docker.pkg.dev/${PROJECT_ID}/erp-pesantren-repo/php8.3-pesantren-base:latest

WORKDIR /var/www/html

# 1. Install Node.js & NPM (Ringan)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Apache Configuration
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 3. Copy Dependencies (Memanfaatkan Cache Layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# 4. Copy Source Code & Build Aset
COPY . .
RUN composer dump-autoload --no-dev --optimize && \
    npm install && \
    npm run build && \
    rm -rf node_modules

# 5. Final Touch
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Cloud Run Port Configuration
EXPOSE 8080
ENV PORT 8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

CMD ["apache2-foreground"]