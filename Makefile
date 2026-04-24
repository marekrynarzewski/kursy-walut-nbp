COMPOSE_PROFILE ?= standalone
DOCKER_COMPOSE ?= docker compose --profile $(COMPOSE_PROFILE)

init:
	cp .env.dist .env

install:
	$(DOCKER_COMPOSE) build app
	$(DOCKER_COMPOSE) run --rm --no-deps app composer install
	$(DOCKER_COMPOSE) up -d
	$(DOCKER_COMPOSE) run --rm --no-deps app php artisan key:generate
	$(DOCKER_COMPOSE) run --rm --no-deps app php artisan migrate