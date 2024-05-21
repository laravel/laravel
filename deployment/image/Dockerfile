# syntax=docker/dockerfile:1
FROM nginx:stable-bookworm@sha256:ce438af5a52c680c72ecc85f9b51f84bbb517fa3cce9c8d25f010ed5ddd415d4 AS server1

WORKDIR /var/www/REPLACEPROJECTNAME

RUN apt update && apt upgrade -y && \
    apt install -y cron && \
    echo "N\n" | apt install -y nginx-common && \
    apt install -y libnginx-mod-http-ndk && \
    apt install -y libnginx-mod-http-lua && \
    apt install -y lsb-release ca-certificates apt-transport-https software-properties-common gnupg2 && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/sury-php.list && \
    curl -fsSL  https://packages.sury.org/php/apt.gpg| gpg --dearmor -o /etc/apt/trusted.gpg.d/sury-keyring.gpg && \
    apt update && \
    apt install -y unzip && \
    apt install -y php8.4-cli \
    php8.4-common \
    php8.4-fpm \
    php8.4-pdo \
    php8.4-opcache \
    php8.4-zip \
    php8.4-phar \
    php8.4-iconv \
    php8.4-cli \
    php8.4-curl \
    php8.4-mbstring \
    php8.4-tokenizer \
    php8.4-fileinfo \
    php8.4-xml \
    php8.4-xmlwriter \
    php8.4-simplexml \
    php8.4-dom \
    php8.4-tokenizer \
    php8.4-intl php8.4-readline && \
    apt install -y openssl php8.4-mysql php8.4-pgsql

#COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
#COPY deployment/image/php.ini /usr/local/etc/php/conf.d/
RUN #echo extension=php_intl.dll >> /etc/php/8.4/cli/conf.d/99-sail.ini

RUN #install-php-extensions ext-intl

#RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
#      ext-intl
#RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
#RUN docker-php-ext-install pdo pdo_pgsql
#RUN #php docker-php-ext-install ext-intl

##########################################################################################

FROM composer:2.8 AS composer1
WORKDIR /app
COPY . .
RUN composer install --optimize-autoloader --no-scripts
RUN composer dump-autoload
# RUN php artisan optimize:clear

##########################################################################################
FROM node:22.11.0-alpine AS node1
WORKDIR /app
RUN npm install -g pnpm
COPY package.json pnpm-lock.yaml ./
ENV APP_ENV=production
RUN pnpm install
COPY . .
RUN npm run build

##########################################################################################


FROM server1

WORKDIR /var/www/REPLACEPROJECTNAME

COPY --from=composer1 /app/vendor ./vendor

COPY --from=node1 --chown=nginx:nginx /app .

COPY deployment/image/dockerconfig/nginx.conf /etc/nginx/nginx.conf
COPY deployment/image/dockerconfig/default.conf /etc/nginx/conf.d/default.conf
COPY deployment/image/dockerconfig/laravel_crons /etc/cron.d/laravel_crons

COPY deployment/image/dockerconfig/php-fpm/www.conf /etc/php/8.4/fpm/pool.d/www.conf
COPY deployment/image/dockerconfig/php-fpm/php-fpm.conf /etc/php/8.4/fpm/php-fpm.conf

RUN chmod 0744 /etc/cron.d/laravel_crons
RUN crontab /etc/cron.d/laravel_crons
RUN touch /var/log/cron.log

RUN echo $(ls)


#RUN php8.4 artisan optimize
#RUN php8.4 artisan config:cache
#RUN php8.4 artisan route:cache
#RUN php8.4 artisan view:cache

RUN php8.4 artisan storage:link

RUN chown -R nginx:nginx /var/www/REPLACEPROJECTNAME
RUN chmod -R 777 /var/www/REPLACEPROJECTNAME/storage
RUN ln -sf /dev/stderr /var/log/fpm-access.log && \
    ln -sf /dev/stderr /var/log/fpm-php.www.log && \
    ln -sf /dev/stderr /var/log/fpm-error.log
RUN apt remove -y lsb-release ca-certificates apt-transport-https software-properties-common gnupg2
RUN apt autoremove -y
EXPOSE 80
ENV APP_ENV=production
CMD ["./deployment/image/docker-entrypoint.sh"]
