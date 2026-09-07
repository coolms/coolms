FROM php:8.5-fpm-alpine

# Only what the skeleton needs. The full CoolMS image additionally builds
# gd, imagick, exif, soap, redis and apcu -- each for a module that is not
# here yet.
#
# zip is NOT one of those: `coolms/core-module` requires ext-zip and is a
# direct dependency of this skeleton, so without it `composer install` exits 2
# and nothing downstream runs. libzip-dev was already installed here and the
# extension was never built -- measured 2026-09-07, from a clean clone.
RUN apk add --no-cache git bash libpq-dev icu-dev libzip-dev autoconf g++ make \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_pgsql pgsql intl zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD ["php-fpm"]
