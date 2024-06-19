 #syntax=docker/dockerfile:1.4

FROM mkldevops/php-fpm-alpine:8.2

COPY --link docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint
COPY --link docker/app.ini /usr/local/etc/php/conf.d/app.ini

USER www-data

EXPOSE 80
ENTRYPOINT ["docker-entrypoint"]
CMD ["symfony", "serve", "--no-tls", "--allow-http", "--port=80"]
