FROM php:8.2-apache

# 1. Install driver MySQL & library yang dibutuhkan
RUN docker-php-ext-install pdo pdo_mysql

# 2. Install unzip dan git (penting buat Composer)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# 3. Aktifkan mod_rewrite Apache
RUN a2enmod rewrite

# 4. Konfigurasi Apache agar mengizinkan .htaccess (MENGATASI 404)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 5. Ganti root folder ke /public (Standar Laravel)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 6. Copy semua file project ke dalam container
COPY . /var/www/html

# 7. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 8. Install dependency Laravel (tanpa dev tools agar ringan)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 9. BUAT SYMLINK (Tambahkan di sini)
# Ini agar folder storage/app/public bisa diakses via public/storage
RUN php artisan storage:link

# 10. Ubah hak akses folder storage, cache, & symlink (Update bagian ini)
# Kita tambahkan folder public/storage agar Apache punya izin membaca link tersebut
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage

# 11. Expose port 80
EXPOSE 80
