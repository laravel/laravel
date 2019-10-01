FROM digbang/php-dev:7.1

RUN apt-get update && apt-get install -y libxml2-dev libicu-dev locales locales-all
RUN docker-php-ext-configure intl
RUN docker-php-ext-install -j$(nproc) soap intl bcmath

RUN mkdir /proxies && chown -R www-data:www-data /proxies && ln -s /proxies /var/www/html/proxies
