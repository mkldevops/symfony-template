#syntax=docker/dockerfile:1.4

FROM mkldevops/php-fpm-alpine:8.2

COPY --link docker/app.ini /usr/local/etc/php/conf.d/app.ini

EXPOSE 80
ENTRYPOINT ["docker-entrypoint"]
CMD ["symfony", "serve", "--no-tls", "--allow-http", "--port=80"]
