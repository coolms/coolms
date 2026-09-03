FROM php:8.5-fpm-alpine

# Only what the skeleton needs. The full CoolMS image additionally builds
# gd, imagick, zip, exif, soap, redis and apcu -- each for a module that is
# not here yet.
RUN apk add --no-cache git bash libpq-dev icu-dev libzip-dev autoconf g++ make \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_pgsql pgsql intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD ["php-fpm"]
