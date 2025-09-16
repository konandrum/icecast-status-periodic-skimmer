stack_name = isps
php_source_tag = dev-alpine
php_image_name = gitlab-registry.idci-consulting.fr/liodie/icecast-status-periodic-skimmer/php-fpm
cron_source_tag = cron-dev-alpine
cron_image_name = gitlab-registry.idci-consulting.fr/liodie/icecast-status-periodic-skimmer/cron
image_tag = dev-alpine

php_sources = src/
php_container_id = $(shell docker ps --filter name="$(stack_name)_php" -q)
user = $(shell id -u)

node_version = 22

default: console

# SHELL
.PHONY: shell
shell:
	docker exec -it $(php_container_id) /bin/sh

.PHONY: bash
bash:
	docker exec -it $(php_container_id) /bin/bash

.PHONY: command
command:
	docker exec -it $(php_container_id) $(cmd)

# UTILS
.PHONY: cs-check
cs-check:
	docker run --rm -i -v `pwd`:`pwd` -w `pwd` cytopia/php-cs-fixer --rules=@Symfony --verbose fix $(php_sources) --dry-run

.PHONY: cs-fix
cs-fix:
	docker run --rm -i -v `pwd`:`pwd` -w `pwd` cytopia/php-cs-fixer --rules=@Symfony --verbose fix $(php_sources)

.PHONY: test-unit
test-unit:
	docker exec -u www-data -it "$(php_container_id)" php vendor/bin/phpunit $(options)

# SYMFONY
.PHONY: console
console:
	docker exec -u www-data -it "$(php_container_id)" php $(php_options) bin/console $(cmd)

.PHONY: composer
composer:
	docker exec -u www-data -it "$(php_container_id)" php -d memory_limit=-1 /usr/local/bin/composer $(cmd)

.PHONY: composer-update
composer-update:
	docker exec -u www-data -it "$(php_container_id)" php -d memory_limit=-1 /usr/local/bin/composer update $(cmd)

.PHONY: composer-install
composer-install:
	docker exec -u www-data -it "$(php_container_id)" php -d memory_limit=-1 /usr/local/bin/composer install --no-interaction

# NODE
.PHONY: yarn
yarn:
	docker run --rm -it -v `pwd`:/usr/src/app -w /usr/src/app node:$(node_version) yarn $(cmd)

.PHONY: encore
encore:
	docker run --rm -it -v `pwd`:/usr/src/app -w /usr/src/app node:$(node_version) yarn encore dev $(options)

.PHONY: encore-production
encore-production:
	docker run --rm -it -v `pwd`:/usr/src/app -w /usr/src/app node:$(node_version) yarn encore production $(options)

# IMAGES
.PHONY: build-images
build-images:
	docker build --target=$(php_source_tag) -t $(php_image_name):$(image_tag) -f .docker/php/Dockerfile .
	docker build --target=$(cron_source_tag) -t $(cron_image_name):$(image_tag) -f .docker/php/Dockerfile .

.PHONY: push-image
push-image:
	docker push $(php_image_name):$(php_source_tag)
	docker push $(cron_image_name):$(cron_source_tag)

# PROJECT
.PHONY: start
start: #build-images
	docker stack deploy -c .docker/docker-compose.yaml $(stack_name)

.PHONY: stop
stop:
	docker stack rm $(stack_name)