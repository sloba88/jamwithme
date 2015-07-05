COMPOSER        = composer install
COMPOSER_UPDATE = composer update --prefer-dist --optimize-autoloader
BOWER           = bower
CONSOLE         = php app/console
CC              = $(CONSOLE) cache:clear
ASSETS          = $(CONSOLE) assets:install --symlink web
ASSETIC_DEV     = $(CONSOLE) assetic:dump
ASSETIC_PROD    = $(CONSOLE) assetic:dump --env=prod
DUMP_ROUTE      = $(CONSOLE) fos:js-routing:dump

default: help

help:
	@echo "dev         - Install for dev"
	@echo "prod        - Install for production"
	@echo "assets-dev  - Install assets for dev"
	@echo "assets-prod - Install assets for production"

dev:
	$(COMPOSER_UPDATE)
	$(BOWER) update
	$(CC)
	$(DUMP_ROUTE)
	$(ASSETS)
	$(ASSETIC_DEV)

prod:
	$(COMPOSER)
	$(BOWER) install
	$(DUMP_ROUTE) --env=prod
	$(CC) --env=prod
	$(ASSETS)
	$(ASSETIC_PROD)

assets-dev:
	$(CC)
	$(DUMP_ROUTE)
	$(ASSETS)
	$(ASSETIC_DEV)

assets-prod:
	$(CC) --env=prod
	$(DUMP_ROUTE) --env=prod
	$(ASSETS)
	$(ASSETIC_PROD)
