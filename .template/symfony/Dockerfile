 #syntax=docker/dockerfile:1.4

FROM dunglas/frankenphp:alpine

# Disable HTTPS
ENV SERVER_NAME=:80
ENV COMPOSER_ALLOW_SUPERUSER=1

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
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh && apk add symfony-cli

# Oh My Zsh
RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)"

WORKDIR /app

COPY --link . /app

RUN symfony composer install --no-interaction --no-progress
