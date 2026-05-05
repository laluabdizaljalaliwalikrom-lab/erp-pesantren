# --- STAGE 1: PHP Dependencies ---
FROM composer:latest AS vendor-builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# --- STAGE 2: Production Image ---
FROM asia-southeast2-docker.pkg.dev/project-abe744c2-29af-4c9b-ab9/erp-pesantren-repo/php8.3-pesantren-base:latest

WORKDIR /var/www/html

# 1. Apache Configuration (Gunakan tanda kutip ganda agar variabel ter-expand)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf
RUN sed -ri -e "s!/var/www/!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 2. Copy Vendor
COPY --from=vendor-builder /app/vendor /var/www/html/vendor

# 3. Cache NPM install
COPY package*.json ./
RUN npm install --no-audit --no-fund

# 4. Copy Source & Build
COPY . .
RUN composer dump-autoload --no-dev --optimize && \
    npm run build && \
    rm -rf node_modules

# 5. Permissions (Pastikan Apache bisa baca folder public)
RUN chown -R www-data:www-data /var/www/html

# 6. Cloud Run Port Configuration (Gunakan tanda kutip ganda)
EXPOSE 8080
ENV PORT 8080
RUN sed -i "s/80/${PORT}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

CMD ["apache2-foreground"]