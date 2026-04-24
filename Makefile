COMPOSE_MODE ?= standalone

ifeq ($(COMPOSE_MODE),portfolio)
DOCKER_COMPOSE ?= docker compose -f docker-compose.yml -f docker-compose-portfolio.yaml
else
DOCKER_COMPOSE ?= docker compose --profile standalone -f docker-compose.yml -f docker-compose-standalone.yaml
endif

init:
	cp .env.dist .env

install:
	$(DOCKER_COMPOSE) build app
	$(DOCKER_COMPOSE) run --rm --no-deps app composer install
	$(DOCKER_COMPOSE) up -d
	$(DOCKER_COMPOSE) run --rm --no-deps app php artisan key:generate
	$(DOCKER_COMPOSE) run --rm --no-deps app php artisan migrate

docs:
	$(DOCKER_COMPOSE) run --rm --no-deps app php artisan l5-swagger:generate
