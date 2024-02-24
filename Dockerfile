FROM composer as builder

WORKDIR /app/

COPY composer.* ./

RUN composer install
RUN composer dump-autoload --optimize


FROM php:8.2-cli

ENV TZ=UTC

## Install openssl for signing requests to fayda
RUN apt-get update && apt-get install -y openssl

COPY . /fayda

COPY --from=builder /app/vendor /fayda/vendor

WORKDIR /fayda

RUN apt-get clean

CMD ["php", "-a"]
