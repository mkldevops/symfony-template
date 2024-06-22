 #syntax=docker/dockerfile:1.4

FROM dunglas/frankenphp:alpine

# add additional extensions here:
RUN install-php-extensions \
	pdo_pgsql \
	gd \
	intl \
	zip \
	opcache

  RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
        build-base \
        zsh \
        shadow \
	;

# Symfony cli
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh && \
  apk add symfony-cli && \
  sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"

WORKDIR /app

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN symfony composer install --no-interaction --no-progress
