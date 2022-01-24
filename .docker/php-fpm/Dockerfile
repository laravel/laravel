# Sử dụng image php:8.0-fpm-alpine
FROM php:8.0-fpm-alpine

LABEL maintainer="DucLS <ducls.coder@gmail.com>"

ARG DEBIAN_FRONTEND=noninteractive

# Cài đặt những thư viện cần thiết
RUN docker-php-ext-install \
 bcmath \
 pdo_mysql

# Tạo thư mục /var/www/html
RUN mkdir -p /var/www/html

# Copy toàn bộ file trong thư mục ./src ở máy local vào trong thư mục /var/www/html ở trong container
COPY ./ /var/www/html
