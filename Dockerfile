FROM php:8.1-cli

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /app
COPY . /app

CMD ["php", "-S", "0.0.0.0:10000", "index.php"]

ENV PORT 10000