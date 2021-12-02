FROM php:8.0-fpm-bullseye
LABEL maintainer="Nozomi Nakamura <itijiku.cathy@gmail.com>"

ENV TZ=Asia/Tokyo \
  # 言語設定
  LANG=ja_JP.UTF-8 \
  LANGUAGE=ja_JP:ja \
  LC_ALL=ja_JP.UTF-8 \
  # composer設定
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get -y install locales libicu-dev libonig-dev libzip-dev unzip \
    && locale-gen ja_JP.UTF-8 \
    && localedef -f UTF-8 -i ja_JP ja_JP.UTF-8 \
    && apt -y install task-japanese locales-all \
    && docker-php-ext-install pdo_mysql mysqli mbstring intl zip bcmath \
    && composer config -g process-timeout 3600 \
    && composer config -g repos.packagist composer https://packagist.org \
    && pecl install xdebug-3.1.1 \
    && docker-php-ext-enable xdebug
WORKDIR /var/www/html