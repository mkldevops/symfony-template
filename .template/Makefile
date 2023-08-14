include .env
ifneq ("$(wildcard .env.local)","")
	include .env.local
endif

isContainerRunning := $(shell docker info > /dev/null 2>&1 && docker ps | grep "${PROJECT_NAME}-api" > /dev/null 2>&1 && echo 1 || echo 0)

DOCKER=docker compose
COMPOSER=symfony composer
CONSOLE=php bin/console
GIT=@git

.DEFAULT_GOAL := docker-sh
env=dev

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?## .*$$)|(^## )' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

sf-cc:
	@chmod -R 777 ./
	@APP_ENV=$(env) $(CONSOLE) c:c

composer-install:
	$(COMPOSER) install $q

composer-update:
	$(COMPOSER) update --with-all-dependencies $q

database-drop:
	@APP_ENV=$(env) $(CONSOLE) doctrine:schema:drop --force --full-database $q

doctrine-migration:
	@APP_ENV=$(env) $(CONSOLE) make:migration $q

doctrine-migrate: ## Apply doctrine migrate
	@APP_ENV=$(env) $(CONSOLE) doctrine:migrations:migrate -n $q

doctrine-schema-create:
	@APP_ENV=$(env) $(CONSOLE) doctrine:schema:create $q

doctrine-reset: database-drop doctrine-migrate
doctrine-apply-migration: doctrine-reset doctrine-migration doctrine-reset  ## Apply doctrine migrate and reset database

fixtures-load: #doctrine-reset ## Load fixtures
	APP_ENV=$(env) $(CONSOLE) hautelook:fixtures:load -n $q

jwt-generate:
	@APP_ENV=$(env) $(CONSOLE) lexik:jwt:generate-keypair --skip-if-exists $q

lint:
	$(CONSOLE) lint:container $q
	$(CONSOLE) lint:yaml --parse-tags config/ $q
	$(CONSOLE) lint:twig templates/ $q
	$(CONSOLE) doctrine:schema:validate --skip-sync $q

stan:
	@APP_ENV=$(env) ./vendor/bin/phpstan analyse $q --memory-limit 256M

cs-fix:
	@APP_ENV=$(env) ./vendor/bin/php-cs-fixer fix $q --allow-risky=yes

rector:
	@APP_ENV=$(env) ./vendor/bin/rector --no-progress-bar $q

infection: ## Run infection tests
	@APP_ENV=$(env) ./vendor/bin/infection --min-msi=80 --min-covered-msi=80 --threads=4 --only-covered --show-mutations --log-verbosity=none $q

analyze: lint stan cs-fix rector tests #infection ## Run all analysis tools

test: env=test
test: ## Run tests
	@APP_ENV=$(env) ./vendor/bin/phpunit $(c)

tests: env=test
tests: database-drop doctrine-schema-create fixtures-load test

## —— Git ————————————————————————————————————————————————————————————————
git-clean-branches: ## Clean merged branches
	@git remote prune origin
	@(git branch --merged | egrep -v "(^\*|main|master|dev)" | xargs git branch -d) || true

git-rebase: ## Rebase the current branch
	$(GIT) pull --rebase $q
	$(GIT) pull --rebase origin main $q

message ?= $(shell git branch --show-current | sed -E 's/^([0-9]+)-([^-]+)-(.+)/\2: #\1 \3/' | sed "s/-/ /g")
git-auto-commit:
	$(GIT) add .
	$(GIT) commit -m "${message}" -q || true

current_branch=$(shell git rev-parse --abbrev-ref HEAD)
git-push:
	$(GIT) push origin "$(current_branch)" --force-with-lease --force-if-includes

#commit: q=-q
commit: analyze git-auto-commit git-push ## Commit and push the current branch

## —— Docker ————————————————————————————————————————————————————————————————
docker-install: Dockerfile docker-compose.yaml docker-down docker-build docker-up docker-ps docker-logs ## Reset and install your environment

docker-is-running:
ifeq ($(isContainerRunning), 1)
	@echo "Docker not running"
	@exit 1
endif

docker-up: ## Start the docker container
	$(DOCKER) up -d

docker-logs: ## List the docker containers
	$(DOCKER) logs -f

docker-ps: ## List the docker containers
	$(DOCKER) ps -a

docker-build: ## Build the docker container
	$(DOCKER) build

docker-down: ## down the stack
	$(DOCKER) down --remove-orphans

docker-sh: docker-up ## Connect to the docker container
	$(DOCKER) exec -it api zsh

docker-restart: docker-down docker-up docker-ps ## Reset the docker container

deploy: git-rebase docker-down docker-build docker-up docker-ps docker-logs ## Deploy the application

