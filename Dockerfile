FROM php:8.1-alpine

# Install PDO MySQL extension
RUN docker-php-ext-install pdo_mysql

WORKDIR /app

COPY . .

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
