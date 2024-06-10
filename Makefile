SHELL = /bin/bash

build:
	./docker/init.sh && \
	./docker/update-database.sh

update:
	./docker/update-database.sh

up:
	docker-compose --file ./docker/docker-compose.yaml up -d --no-recreate --remove-orphans

up-build:
	docker-compose --file ./docker/docker-compose.yaml up -d --build

stop:
	docker-compose --file ./docker/docker-compose.yaml stop

down:
	docker-compose --file ./docker/docker-compose.yaml down

down-v:
	docker-compose --file ./docker/docker-compose.yaml down -v

restart:
	docker-compose --file ./docker/docker-compose.yaml restart

wp:
	docker-compose --file ./docker/docker-compose.yaml run wp $(filter-out $@,$(MAKECMDGOALS))

share:
	./docker/live-share.sh