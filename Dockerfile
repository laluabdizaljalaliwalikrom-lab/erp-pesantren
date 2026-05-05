# --- STAGE 1: PHP Dependencies ---
FROM composer:latest AS vendor-builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# --- STAGE 2: Production Image ---
# Hardcoded base image path for stability and speed
FROM asia-southeast2-docker.pkg.dev/project-abe744c2-29af-4c9b-ab9/erp-pesantren-repo/php8.3-pesantren-base:latest

WORKDIR /var/www/html

# 1. Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Copy Vendor (Instant from Stage 1)
COPY --from=vendor-builder /app/vendor /var/www/html/vendor

# 3. Cache NPM install layer
COPY package*.json ./
RUN npm install

# 4. Copy Application Source
COPY . .

# 5. Finalize & Build Assets
RUN composer dump-autoload --no-dev --optimize && \
    npm run build && \
    rm -rf node_modules

# 6. Permissions & Port
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
EXPOSE 8080
ENV PORT 8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

CMD ["apache2-foreground"]