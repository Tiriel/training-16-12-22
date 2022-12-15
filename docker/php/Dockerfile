FROM php:8.1-fpm-alpine3.15

ENV APCU_VERSION 5.1.20
ENV GECKODRIVER_VERSION 0.29.0

# Basic deps
# First "apk add" is necessary for PHP to work with native libs
# Second "apk add" is only for building dependencies (that's why it's removed from cache at the end)
RUN apk add --update --no-cache \
    && apk add \
        libjpeg-turbo-dev \
        libpng-dev \
        firefox \
        freetype-dev \
        icu-dev \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        ca-certificates \
        curl \
        git \
        libmcrypt-dev \
        libzip-dev \
        openssh-client \
        unzip \
        zlib-dev \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
    && yes | pecl install apcu-${APCU_VERSION} \
    && yes | pecl install pcov \
    && apk add php8-pecl-xdebug \
    && docker-php-ext-install zip \
    && docker-php-ext-enable apcu \
    && docker-php-ext-enable opcache \
    && docker-php-ext-configure gd \
    && export NPROC=$(nproc || grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && docker-php-ext-install -j${NPROC} gd \
    && version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
    && apk add --no-cache su-exec \
    && apk add --no-cache bash \
    && curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli \
    && addgroup bar \
    && adduser -D -h /home -s /bin/sh -G bar foo \
    && apk del .build-deps

# PHP config
COPY php.ini /usr/local/etc/php/
COPY symfony.ini /usr/local/etc/php/conf.d/
COPY xdebug.ini /usr/local/etc/php/conf.d/

# Install composer
ADD ./bin/* /usr/local/bin/
RUN install-composer.sh

ENTRYPOINT ["entrypoint.sh"]
