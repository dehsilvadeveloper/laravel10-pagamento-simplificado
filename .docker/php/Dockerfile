# Defining base image
FROM php:8.2-fpm-alpine

# Defining arguments
ARG user=developer
ARG uid=1000

# Steps of this layer:
# - Install system dependencies
# - Install and enable PHP extensions
# - Remove packages that are only needed for build, not for run-time (apk del ...)
# - Clear APK cache and temporary files (rm -rf /var/cache/apk/* && rm -rf /tmp/pear)
RUN apk update && apk --no-cache --virtual add \
        autoconf \
        bash \
        build-base \
        curl \
        gd \
        icu \
        icu-dev \
        iputils-ping \
        libmcrypt-dev \
        libjpeg-turbo \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        net-tools \
        oniguruma-dev \
        openssl \
        openssl-dev \
        unzip \
        zip \
    && pecl channel-update pecl.php.net \
    && pecl install -o -f redis pcov \
    && docker-php-ext-install bcmath exif gd intl mbstring opcache pcntl pdo pdo_mysql xml zip \
    && docker-php-ext-enable redis pcov \
    && apk del autoconf build-base icu-dev libmcrypt-dev libpng-dev libxml2-dev libzip-dev oniguruma-dev openssl-dev \
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/pear

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN addgroup -g $uid $user \
    && adduser -u $uid -G $user -h /home/$user -s /bin/bash -D $user \
    && addgroup $user root \
    && addgroup $user www-data \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www
    
# Copy custom.ini of PHP to the final directory
COPY ./custom.ini /etc/php/conf.d/custom.ini

# Steps of this layer:
# - Remove possible unnecessary directory (to avoid errors with symbolic link)
# - Create symbolic link for the public folder
RUN rm -rf /var/www/html \
    && ln -s public html

# Define user been used
USER $user

# Expose port
EXPOSE 9000
