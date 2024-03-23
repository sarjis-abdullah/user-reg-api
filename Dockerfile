FROM php:8.1-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

RUN pecl install xdebug && docker-php-ext-enable xdebug
# Install system dependencies

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    libzip-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN apt-get -y update \
&& apt-get install -y libicu-dev\
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl

RUN docker-php-ext-install xml
RUN docker-php-ext-install pdo pdo_mysql zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user
