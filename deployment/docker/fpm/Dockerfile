FROM php:8.1-fpm-alpine3.16 as fpm

RUN apk add \
    curl \
    bash \
    ca-certificates \
    libc6-compat && \
    ln -s /lib/libc.musl-x86_64.so.1 /lib/ld-linux-x86-64.so.2

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions bcmath intl opcache soap zip mcrypt igbinary opencensus mysqli pdo pdo_sqlite pdo_mysql mbstring

COPY ./deployment/docker/fpm/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

WORKDIR /var/www

COPY . .
RUN chown -R www-data:www-data /var/www

EXPOSE 9000

# dev target
FROM fpm as fpm-dev

RUN install-php-extensions xdebug

COPY ./deployment/docker/fpm/php-dev.ini /usr/local/etc/php/conf.d/z-php-dev.ini
