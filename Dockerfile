FROM php:8.2-cli

RUN apt-get update && apt-get install -y git unzip libpq-dev libonig-dev
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /app

COPY . /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

CMD php -S 0.0.0.0:8000 -t public
