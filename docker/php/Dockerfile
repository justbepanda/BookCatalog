FROM php:8.2-fpm

ARG UID=1000
ARG GID=1000

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    mariadb-client \
    netcat-openbsd \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif \
    && rm -rf /var/lib/apt/lists/*

RUN curl -o /usr/local/bin/wait-for-it.sh https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh \
    && chmod +x /usr/local/bin/wait-for-it.sh

RUN addgroup --gid $GID devgroup \
    && adduser --disabled-password --gecos "" --uid $UID --gid $GID devuser

USER $UID:$GID

RUN git config --global --add safe.directory /var/www

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www