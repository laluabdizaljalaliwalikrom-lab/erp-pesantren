# --- STAGE 1: Composer dependencies ---
FROM composer:latest AS vendor-builder
WORKDIR /app
COPY composer.json composer.lock ./
# Cache composer downloads
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# --- STAGE 2: Final Production ---
FROM asia-southeast2-docker.pkg.dev/project-abe744c2-29af-4c9b-ab9/erp-pesantren-repo/php8.3-pesantren-base:latest

WORKDIR /var/www/html

# 1. Copy Vendor (Instant)
COPY --from=vendor-builder /app/vendor /var/www/html/vendor

# 2. Copy Node config & Cache install
COPY package*.json ./
RUN npm install --no-audit --no-fund

# 3. Copy App Source
COPY . .

# 4. Fast Build
RUN composer dump-autoload --no-dev --optimize && \
    npm run build && \
    rm -rf node_modules

# 5. Config
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
EXPOSE 8080
ENV PORT 8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

CMD ["apache2-foreground"]