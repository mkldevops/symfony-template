.DEFAULT_GOAL := init-project

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?## .*$$)|(^## )' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

init-project: install-software
	castor init-project

install-software:
	@which castor >/dev/null 2>&1 || ( curl "https://castor.jolicode.com/install" | bash && sudo  mv /home/gitpod/.local/bin/castor /usr/local/bin/castor )
	@which symfony >/dev/null 2>&1 || ( curl -sS https://get.symfony.com/cli/installer | bash && sudo mv /home/gitpod/.symfony5/bin/symfony /usr/local/bin/symfony )
