FROM digbang/php-dev:7.4

COPY conf.d /usr/local/etc/php/conf.d/

RUN mkdir /proxies && chown -R www-data:www-data /proxies && ln -s /proxies /var/www/html/proxies
