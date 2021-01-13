ARG DATABASE_URL

# Dockerfile
FROM gitlab.comune.intranet:5050/docker/php7.4-apache as build

ENV APP_ENV=prod
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
    rm -rf var && \
    rm -rf .env.local && \
    mkdir var && \ 
    chmod 777 -R var && \ 
    composer install --no-dev --optimize-autoloader --no-interaction && \
    yarn install --force && \
    yarn build && \
    yarn outdated \
    rm -rf node_modules

FROM gitlab.comune.intranet:5050/docker/php7.4-apache

ENV DATABASE_URL=sqlite:///%kernel.project_dir%/var/dbapp.sqlite

ENV HTTP_PROXY "http://proxyhttp.comune.intranet:8080"
ENV HTTPS_PROXY "http://proxyhttps.comune.intranet:8080"
ENV NO_PROXY "localhost,127.0.0.1,.localhost,.comune.intranet"
ENV http_proxy "http://proxyhttp.comune.intranet:8080"
ENV https_proxy "http://proxyhttps.comune.intranet:8080"
ENV no_proxy "localhost,127.0.0.1,.localhost,.comune.intranet" 

ARG CI_PROJECT_NAME

WORKDIR /var/www/html
COPY --from=build /app /var/www/html
RUN env
#Per reverse proxy
RUN ln -s ../public public/$CI_PROJECT_NAME
RUN ln -s ../public public/$CI_PROJECT_NAME"test"
RUN chown -R www-data:www-data /var/www

#Remove apache logs folder to override it
RUN rm -rf /var/log/apache2
RUN mkdir /var/log/apache2
#COPY --from=build /app/.docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY --from=build /app/.docker/apache/start-apache /usr/local/bin/


#fix for dh key too small
RUN sed -ri -e 's/(MinProtocol\s*=\s*)TLSv1\.2/\1None/' /etc/ssl/openssl.cnf
RUN sed -ri -e 's/(CipherString\s*=\s*DEFAULT)@SECLEVEL=2/\1/' /etc/ssl/openssl.cnf

RUN chmod +x /usr/local/bin/start-apache
RUN a2enmod rewrite
RUN apachectl configtest

ENV TZ=Europe/Rome
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

EXPOSE 80

CMD ["/usr/local/bin/start-apache"]
