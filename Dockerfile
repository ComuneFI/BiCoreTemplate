ARG DATABASE_URL

# Dockerfile
FROM gitlab.comune.intranet:5050/docker/php7.4-apache as build

ENV APP_ENV=dev
# Database needed only for initial profiling users
ENV DATABASE_URL=sqlite:///%kernel.project_dir%/var/dbapp.sqlite

ENV HTTP_PROXY "http://proxyhttp.comune.intranet:8080"
ENV HTTPS_PROXY "http://proxyhttps.comune.intranet:8080"
ENV NO_PROXY "localhost,127.0.0.1,.localhost,.comune.intranet"
ENV http_proxy "http://proxyhttp.comune.intranet:8080"
ENV https_proxy "http://proxyhttps.comune.intranet:8080"
ENV no_proxy "localhost,127.0.0.1,.localhost,.comune.intranet"

WORKDIR /app
COPY . /app

RUN rm -rf /app/var/cache/dev && \
rm -rf /app/var/cache/prod && \
rm -rf .env.local

RUN rm -rf .git && \
    rm -rf config/jwt && \
    rm -rf node_modules && \
    rm -rf var && \
    rm -rf .env.local && \
    mkdir var && \ 
    chmod 777 -R var && \ 
    composer install

FROM php:7-apache

ENV DATABASE_URL=sqlite:///%kernel.project_dir%/var/dbapp.sqlite

ENV HTTP_PROXY "http://proxyhttp.comune.intranet:8080"
ENV HTTPS_PROXY "http://proxyhttps.comune.intranet:8080"
ENV NO_PROXY "localhost,127.0.0.1,.localhost,.comune.intranet"
ENV http_proxy "http://proxyhttp.comune.intranet:8080"
ENV https_proxy "http://proxyhttps.comune.intranet:8080"
ENV no_proxy "localhost,127.0.0.1,.localhost,.comune.intranet" 

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev libpq-dev git \
        libmcrypt-dev libonig-dev zlib1g-dev \
        acl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql mbstring gd zip

WORKDIR /var/www/
COPY --from=build /app /var/www/

#Per reverse proxy
RUN ln -s /var/www/public /var/www/public/$CI_PROJECT_NAME
RUN chown -R www-data:www-data /var/www

COPY --from=build /app/.docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY --from=build /app/.docker/apache/start-apache /usr/local/bin/


#fix for dh key too small
RUN sed -ri -e 's/(MinProtocol\s*=\s*)TLSv1\.2/\1None/' /etc/ssl/openssl.cnf
RUN sed -ri -e 's/(CipherString\s*=\s*DEFAULT)@SECLEVEL=2/\1/' /etc/ssl/openssl.cnf

RUN chmod +x /usr/local/bin/start-apache
RUN a2enmod rewrite

EXPOSE 80

CMD ["/usr/local/bin/start-apache"]
