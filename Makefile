init:
	cp .env.dist .env

install:
	docker-compose build
	docker-compose up -d
	docker-compose run app composer install