ARG NODE_VERSION=24.8.0

#####################
### STAGE 0: Base ###
#####################

FROM php:8.4-fpm AS base

## Install dependencies
RUN apt update \
    && apt install -y git rsync libzip-dev zip libpq-dev libicu-dev \
    # Install pdo & pgsql
    && docker-php-ext-install pdo pgsql pdo_pgsql \
    # Install intl
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    # Install redis
    && pecl install redis \
    && docker-php-ext-enable redis \
    # Install opcache
    && docker-php-ext-install opcache \
    # Instal zip
    && docker-php-ext-install zip \
    # Instal pcntl
    && docker-php-ext-configure pcntl \
    && docker-php-ext-install pcntl

## Add php.ini configuration
ADD .docker/php/php.ini /usr/local/etc/php/php.ini
ADD .docker/php/entrypoint.sh /entrypoint.sh

WORKDIR /var/www/app

## Copy App files
COPY composer.json composer.lock symfony.lock .env* package.json webpack.config.js ./
COPY assets assets/
COPY bin bin/
COPY config config/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY templates templates/
#COPY translations translations/

## Create empty var cache and var log directory
RUN mkdir -p /var/www/app/var/cache /var/www/app/var/log

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm", "-F"]

######################
### STAGE 1: Build ###
######################

FROM base AS build

## Install composer 2
ADD ["https://getcomposer.org/installer", "https://composer.github.io/installer.sig", "./"]
RUN php ./installer --2 --install-dir="/usr/local/bin/" --filename="composer" && rm -f installer installer.sig
RUN mkdir -p /var/www/.composer/cache && chown -R www-data /var/www/.composer

## Install App dependencies (using composer in PROD env)
ARG APP_ENV=prod
ENV APP_ENV=${APP_ENV}
RUN set -eux; \
    COMPOSER_ALLOW_SUPERUSER=1 composer install --no-scripts --no-suggest --no-interaction --no-dev --prefer-dist --optimize-autoloader; \
    composer dump-autoload --optimize --classmap-authoritative;

## Install App assets
RUN php /var/www/app/bin/console assets:install

## Install node and npm using nvm
ENV NVM_DIR=/usr/local/nvm
ENV NODE_VERSION=24

## Build assets
RUN curl --silent -o- https://raw.githubusercontent.com/creationix/nvm/v0.31.2/install.sh | bash -
RUN . $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default \
    && npm install --global yarn \
    && yarn --version \
    && yarn install \
    && yarn encore production

ENV NODE_PATH=$NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH=$NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

####################
### STAGE 2: Dev ###
####################

FROM build AS dev

## Install xDebug (putted at the end - CLUDGE)
RUN pecl install xdebug-3.4.2 && docker-php-ext-enable xdebug

#########################
### STAGE 3: Cron-Dev ###
#########################

FROM dev AS cron-dev

RUN apt update && apt install -y cron

## Copy cron file to the cron.d directory
ADD .docker/cron/crontab /etc/cron.d/isps-cron

## Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/isps-cron

## Apply cron job
RUN crontab /etc/cron.d/isps-cron

ENTRYPOINT []
CMD ["cron", "-f"]

#####################
### STAGE 4: Prod ###
#####################

FROM base AS prod

## Define environment vars
ARG APP_VERSION
ENV APP_VERSION=${APP_VERSION}
ARG APP_ENV=prod
ENV APP_ENV=${APP_ENV}

## Copy built vendors
COPY --from=build /var/www/app/vendor /var/www/app/vendor

## Copy built assets and bundles in order to use and share them (entrypoint)
COPY --from=build /var/www/app/public/build /usr/local/share/public/build/

## Define volumes
VOLUME /var/www/app/var/cache
VOLUME /var/www/app/var/log
VOLUME /var/www/app/var/import
VOLUME /var/www/app/public/build
VOLUME /tmp

##########################
### STAGE 5: Cron-Prod ###
##########################

FROM prod AS cron

RUN apt update && apt install -y cron

## Copy cron file to the cron.d directory
ADD .docker/cron/crontab /etc/cron.d/isps-cron

## Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/isps-cron

## Apply cron job
RUN crontab /etc/cron.d/isps-cron

ENTRYPOINT []
CMD ["cron", "-f"]


############################
### STAGE 0: Base-alpine ###
############################

FROM php:8.4-fpm-alpine AS base-alpine

## Install dependencies
RUN apk update && apk upgrade \
    && apk add --no-cache linux-headers autoconf bash gcc git rsync libzip-dev zip libpq-dev icu-dev $PHPIZE_DEPS \
    # Install pdo & pgsql
    && docker-php-ext-install pdo pgsql pdo_pgsql \
    # Install intl
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
#    # Install redis
#    && pecl install redis \
#    && docker-php-ext-enable redis
    # Install opcache
    && docker-php-ext-install opcache \
    # Instal zip
    && docker-php-ext-install zip \
    # Instal pcntl
    && docker-php-ext-configure pcntl \
    && docker-php-ext-install pcntl

## Add php.ini configuration
ADD .docker/php/php.ini /usr/local/etc/php/php.ini
ADD .docker/php/entrypoint.sh /entrypoint.sh

WORKDIR /var/www/app

## Copy App files
COPY composer.json composer.lock symfony.lock .env* package.json webpack.config.js ./
COPY assets assets/
COPY bin bin/
COPY config config/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY templates templates/
#COPY translations translations/

## Create empty var cache and var log directory
RUN mkdir -p /var/www/app/var/cache /var/www/app/var/log

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm", "-F"]

#############################
### STAGE 1: Build-alpine ###
#############################
FROM node:${NODE_VERSION}-alpine AS node

FROM base-alpine AS build-alpine

## Install composer 2
ADD ["https://getcomposer.org/installer", "https://composer.github.io/installer.sig", "./"]
RUN php ./installer --2 --install-dir="/usr/local/bin/" --filename="composer" && rm -f installer installer.sig
RUN mkdir -p /var/www/.composer/cache && chown -R www-data /var/www/.composer

## Install App dependencies (using composer in PROD env)
ARG APP_ENV=prod
ENV APP_ENV=${APP_ENV}
RUN set -eux; \
    COMPOSER_ALLOW_SUPERUSER=1 composer install --no-scripts --no-suggest --no-interaction --no-dev --prefer-dist --optimize-autoloader; \
    composer dump-autoload --optimize --classmap-authoritative;

## Install App assets
RUN php /var/www/app/bin/console assets:install

## Install node and yarn
COPY --from=node /usr/lib /usr/lib
COPY --from=node /usr/local/lib /usr/local/lib
COPY --from=node /usr/local/include /usr/local/include
COPY --from=node /usr/local/bin /usr/local/bin

RUN node -v && npm install -g yarn --force

## Build assets
RUN yarn --version \
    && yarn install \
    && yarn encore production

###########################
### STAGE 2: Dev-alpine ###
###########################

FROM build-alpine AS dev-alpine

## Install xDebug (putted at the end - CLUDGE)
RUN pecl install xdebug-3.4.2 && docker-php-ext-enable xdebug

################################
### STAGE 3: Cron-Dev-alpine ###
################################

FROM dev-alpine AS cron-dev-alpine

RUN apk update && apk upgrade && apk add busybox

## Copy cron file to the cron.d directory
ADD .docker/cron/crontab /etc/cron.d/isps-cron

## Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/isps-cron

## Apply cron job
RUN crontab /etc/cron.d/isps-cron

ENTRYPOINT []
CMD ["crond", "-f"]

############################
### STAGE 4: Prod-alpine ###
############################

FROM base-alpine AS prod-alpine

## Define environment vars
ARG APP_VERSION
ENV APP_VERSION=${APP_VERSION}
ARG APP_ENV=prod
ENV APP_ENV=${APP_ENV}

## Copy built vendors
COPY --from=build-alpine /var/www/app/vendor /var/www/app/vendor

## Copy built assets and bundles in order to use and share them (entrypoint)
COPY --from=build-alpine /var/www/app/public/build /usr/local/share/public/build/

## Define volumes
VOLUME /var/www/app/var/cache
VOLUME /var/www/app/var/log
VOLUME /var/www/app/var/import
VOLUME /var/www/app/public/build
VOLUME /tmp

#################################
### STAGE 5: Cron-Prod-alpine ###
#################################

FROM prod-alpine AS cron-alpine

RUN apk update && apk upgrade && apk add busybox

## Copy cron file to the cron.d directory
ADD .docker/cron/crontab /etc/cron.d/isps-cron

## Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/isps-cron

## Apply cron job
RUN crontab /etc/cron.d/isps-cron

ENTRYPOINT []
CMD ["crond", "-f"]