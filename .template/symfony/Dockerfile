 #syntax=docker/dockerfile:1.4

FROM mkldevops/frankenphp:8.3

# Disable HTTPS
ENV SERVER_NAME=:80

WORKDIR /app

COPY --link . /app

RUN symfony composer install --no-interaction --no-progress
