FROM php:8.2-fpm

ARG UID=1000
ARG GID=1000

# Установка системных пакетов
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

# Настройка PHP
RUN echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

RUN curl -o /usr/local/bin/wait-for-it.sh https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh \
    && chmod +x /usr/local/bin/wait-for-it.sh

# Создание пользователя и необходимых директорий
RUN addgroup --gid $GID devgroup \
    && adduser --disabled-password --gecos "" --uid $UID --gid $GID devuser

# Создаем все нужные папки, включая runtime
RUN mkdir -p /var/www/images/books /var/www/protected/runtime \
    && chown -R $UID:$GID /var/www

# Создаем лог-файл и даем права devuser
RUN touch /var/www/protected/runtime/sms_cron.log \
    && chown $UID:$GID /var/www/protected/runtime/sms_cron.log

# Окружение пользователя
USER $UID:$GID
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN git config --global --add safe.directory /var/www

WORKDIR /var/www

# Запуск
USER root

CMD sh -c "chmod +x /var/www/protected/yiic || true; \
    (while true; do php /var/www/protected/yiic smsqueue process >> /var/www/protected/runtime/sms_cron.log 2>&1; sleep 60; done &) \
    && php-fpm -F"