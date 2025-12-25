FROM php:8.2-apache

# Install driver MySQL (PENTING: karena kamu pakai MySQL/TiDB)
RUN docker-php-ext-install pdo pdo_mysql

# Install unzip dan git (penting buat Composer)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Aktifkan mod_rewrite (supaya URL Laravel jalan cantik)
RUN a2enmod rewrite

# Ganti root folder ke /public (Standar Laravel)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copy semua file project kamu ke dalam container
COPY . /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# Ubah hak akses folder storage supaya bisa di-write
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
