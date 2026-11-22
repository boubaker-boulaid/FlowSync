FROM php:8.5-fpm

ARG UID=1000
ARG GID=1000

# C libraries the PHP extensions compile against, plus tools Composer needs
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        unzip \
        git \
    && rm -rf /var/lib/apt/lists/*

# Bundled extensions via docker-php-ext-install; redis comes from PECL
RUN docker-php-ext-install -j"$(nproc)" pdo_mysql intl bcmath pcntl zip \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Non-root user matching the host UID/GID so files in backend/ belong to you
RUN groupadd -g ${GID} app \
    && useradd -u ${UID} -g app -m -s /bin/bash app

WORKDIR /var/www/backend
USER app
