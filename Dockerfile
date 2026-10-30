FROM php:8.3-apache AS runtime
RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev libxml2-dev \
    && docker-php-ext-install -j2 mysqli mbstring dom xml xmlwriter \
    && rm -rf /var/lib/apt/lists/*
COPY docker/php.ini /usr/local/etc/php/conf.d/arrue.ini
COPY docker/apache.conf /etc/apache2/conf-available/arrue.conf
RUN a2enmod headers && a2enconf arrue

FROM runtime AS app
ENV APP_WEB_ROOT=/var/www/html
COPY erronka2_web/ /var/www/html/
COPY tools/ /opt/arrue-tools/
RUN chown -R www-data:www-data /var/www/html/images /var/www/html/perfiles
