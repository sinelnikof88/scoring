FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    bash \
    && docker-php-ext-install \
        intl \
        zip \
        pdo_mysql \
        opcache \
        mbstring

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Xdebug (опционально, можно не ставить)
# RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
#     && pecl install xdebug \
#     && docker-php-ext-enable xdebug

WORKDIR /app/scoring