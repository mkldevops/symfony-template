.DEFAULT_GOAL := init-project

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?## .*$$)|(^## )' Makefile | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

init-project:
	wich castor &> /dev/null || echo 'Castor is not installed. Please install it on https://castor.jolicode.com/getting-started/installation.'
	castor init-project
