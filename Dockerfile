FROM node:14.0.0 as build

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN npx browserslist@latest --update-db


RUN npm run build

FROM php:8.1-fpm-alpine

RUN apk update && apk add curl && \
        curl -sS https://getcomposer.org/installer | php \
        && chmod +x composer.phar && mv composer.phar /usr/local/bin/composer

RUN apk update && \
        apk add --no-cache \
        libzip-dev \
        zip \
        unzip \
        icu-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        postgresql-dev && \
        docker-php-ext-configure gd --with-freetype --with-jpeg && \
        docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli zip intl opcache


WORKDIR /app

# COPY --from=builder /app/vendor /app

COPY composer.json composer.lock ./

COPY . .
RUN chmod +x artisan

# RUN composer dump-autoload --optimize && composer run-script post-install-cmd

EXPOSE 8000

CMD [ "php", "artisan", "serve" && "php", "artisan", "queue:work"]