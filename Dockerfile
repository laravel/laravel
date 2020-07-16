FROM nginx:alpine

EXPOSE 80

RUN apk update
RUN apk upgrade
RUN apk add php7 php7-fpm php7-fileinfo php7-dom php7-tokenizer php7-session composer

WORKDIR /app

COPY . .

RUN rm Dockerfile
RUN rm -r /usr/share/nginx/html

RUN chmod -R 777 .

RUN mv nginx-docker.conf /etc/nginx/conf.d/default.conf

RUN composer install --optimize-autoloader --no-dev

RUN php artisan config:cache
RUN php artisan view:cache

# Caching the routes still throws an error which hangs the build:
# https://github.com/laravel/framework/issues/22034
#RUN php artisan route:cache

RUN echo "php-fpm7" > /run.sh
RUN echo "nginx -g 'daemon off;'" >> /run.sh

RUN chmod 500 /run.sh

CMD ["sh", "/run.sh"]
