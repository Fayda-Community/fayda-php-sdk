FROM composer as builder

WORKDIR /app/

COPY composer.* ./

RUN composer install
RUN composer dump-autoload --optimize

# openssl component supplied since PHP8.2 has a legacy function removed, thus using the next most recent release
# TODO(Anteneh): check a means to support PHP 8.2 and above

FROM php:8.1.13-cli

ENV TZ=UTC

## Install openssl for signing requests to fayda
RUN apt-get update && apt-get install -y openssl

COPY . /fayda

COPY --from=builder /app/vendor /fayda/vendor

WORKDIR /fayda

RUN apt-get clean

CMD ["php", "-a"]
