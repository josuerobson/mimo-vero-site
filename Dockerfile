FROM php:8.2-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set recommended PHP settings
RUN { \
    echo 'display_errors=Off'; \
    echo 'error_reporting=E_ALL & ~E_DEPRECATED & ~E_STRICT'; \
    echo 'upload_max_filesize=10M'; \
    echo 'post_max_size=12M'; \
    echo 'memory_limit=256M'; \
    echo 'date.timezone=America/Sao_Paulo'; \
} > /usr/local/etc/php/conf.d/custom.ini

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Apache rewrite for clean URLs
RUN { \
    echo '<Directory /var/www/html>'; \
    echo '    Options -Indexes +FollowSymLinks'; \
    echo '    AllowOverride All'; \
    echo '    Require all granted'; \
    echo '</Directory>'; \
} > /etc/apache2/conf-available/app.conf \
    && a2enconf app

EXPOSE 80

CMD ["apache2-foreground"]
