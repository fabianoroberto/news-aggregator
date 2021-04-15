FROM php:8.0-fpm-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

COPY wait-for-it.sh /usr/bin/wait-for-it

RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git
RUN apk add bash

RUN docker-php-ext-install pdo_mysql

RUN install-php-extensions opcache xdebug @composer
RUN install-php-extensions amqp intl zip

#RUN install-php-extensions \
#    amqp \
#    intl \
#    openssl \
#    redis \
#    supervisor \
#    xsl \
#    zip

RUN wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www
CMD composer install ; wait-for-it database:3306 -- bin/console doctrine:migrations:migrate ; php-fpm
EXPOSE 9000