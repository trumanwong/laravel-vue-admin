FROM php:8.1-fpm

WORKDIR /var/www

# Update packages

RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get update \
    && apt-get install -y supervisor nodejs netcat libmcrypt-dev libzip-dev libonig-dev libpng-dev libwebp-dev  libjpeg62-turbo-dev  libpng-dev libxpm-dev  libfreetype6-dev libbz2-dev git \
    && apt-get clean && rm -rf /etc/supervisor/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install gd
RUN pecl install -o -f redis &&  rm -rf /tmp/pear &&  docker-php-ext-enable redis

COPY . .
COPY .env.docker .env
COPY supervisor /etc/supervisor

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["bash", "./cmd.sh"]

