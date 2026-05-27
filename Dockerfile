FROM php:8.2-apache

# MPM first: mod_php requires exactly one MPM (prefork)
RUN set -eux; \
    a2dismod -f mpm_event mpm_worker 2>/dev/null || true; \
    rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf \
          /etc/apache2/mods-enabled/mpm_itk.load /etc/apache2/mods-enabled/mpm_itk.conf; \
    a2enmod mpm_prefork

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Re-apply after PHP extensions (prevents AH00534 if extra MPM symlinks reappear)
RUN a2dismod mpm_event mpm_worker || true \
    && rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf \
             /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf \
    && a2enmod mpm_prefork rewrite headers

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/html
COPY . /var/www/html

RUN mkdir -p uploads/images uploads/videos uploads/materials \
    dashboard/uploads/images dashboard/uploads/videos dashboard/uploads/materials \
    && chown -R www-data:www-data uploads dashboard/uploads \
    && chmod -R 775 uploads dashboard/uploads

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
