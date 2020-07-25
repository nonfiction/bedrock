include .env 

# Use PUBLIC_HOST if defined, otherwise set to APP_NAME + REMOTE_HOST
PUBLIC_HOST := $(if $(PUBLIC_HOST),$(PUBLIC_HOST),$(APP_NAME).$(REMOTE_HOST))

# docker-compose commands for development, production, and remote production
docker-compose := docker-compose
docker-compose-prod := docker-compose -f docker-compose.yml -f docker-compose.production.yml
docker-compose-remote := DOCKER_HOST=ssh://root@$(REMOTE_HOST) APP_HOST=$(REMOTE_HOST) docker-compose -f docker-compose.yml -f docker-compose.production.yml

# append bedrock's docker-compose file if available
ifneq ("$(wildcard bedrock)","")
  docker-compose += -f docker-compose.yml -f docker-compose.override.yml -f bedrock/docker-compose.override.yml
  docker-compose-prod += -f bedrock/docker-compose.override.yml
endif

all:
	@echo "【 $(APP_NAME)@$(APP_HOST) --> $(REMOTE_HOST) --> $(PUBLIC_HOST) 】"
	@echo "   ‣ install ‣ upgrade ‣ remote"
	@echo "   ‣ build ‣ build! ‣ assets"
	@echo "   ‣ up ‣ upp ‣ up!"
	@echo "   ‣ down ‣ down!"
	@echo "   ‣ logs ‣ logs!"
	@echo "   ‣ pull ‣ push"
	@echo "   ‣ export ‣ import ‣ clean"
	@echo "   ‣ plugin add=WP_PLUGIN ‣ theme add=WP_THEME ‣ package add=NPM_PACKAGE"
	@echo "   ‣ shell ‣ shell! ‣ host!"
	@echo ""

up: build update-db         ; $(docker-compose) up --remove-orphans -d dev
upp: assets build update-db ; $(docker-compose-prod) up --remove-orphans -d srv
down:                       ; $(docker-compose) down --remove-orphans
logs:                       ; $(docker-compose) logs -f

remote: ; $(docker-compose) run --rm env remote_host
dotenv: tmp-dir ; $(docker-compose) run --rm env dotenv

up!: assets build! update-db! ; $(docker-compose-remote) up --remove-orphans -d srv
down!:                        ; $(docker-compose-remote) down --remove-orphans
logs!:                        ; $(docker-compose-remote) logs -f

.env:
	docker run --rm -it \
		-e APP_NAME=$(notdir $(shell pwd)) \
		-e APP_HOST=$(shell hostname -f) \
		-v $(PWD):/srv \
		nonfiction/bedrock:env dotenv

assets:
	$(docker-compose-prod) run --rm dev npx ncu -u
	$(docker-compose-prod) run --rm dev npm update --save-dev --prefix /srv/node_modules
	$(docker-compose-prod) run --rm dev webpack --progress

build: tmp-dir     ; $(docker-compose) build srv
rebuild: tmp-dir   ; $(docker-compose) build --pull --no-cache srv
build!: tmp-dir!   ; $(docker-compose-remote) build srv
rebuild!: tmp-dir! ; $(docker-compose-remote) build --pull --no-cache srv

upgrade: .env assets rebuild update-db

# make plugin/theme/package add=name_of_package
plugin:  ; test $(add) && $(docker-compose) run --rm srv composer require --no-update wpackagist-plugin/$(add)
theme:   ; test $(add) && $(docker-compose) run --rm srv composer require --no-update wpackagist-theme/$(add)
package: ; test $(add) && $(docker-compose) run --rm dev npm install $(add) --save-dev --prefix /srv/node_modules

tmp-dir:  ; @mkdir -p /tmp/$(APP_NAME)/uploads
tmp-dir!: ; @ssh root@$(REMOTE_HOST) mkdir -p /tmp/$(APP_NAME)/uploads

db:         ; $(docker-compose) run --rm env create_db
export-db:  ; $(docker-compose) run --rm -e UID=$(shell id -u) env export_db
import-db:  ; $(docker-compose) run --rm env import_db

update-db:  ; $(docker-compose) run --rm wp core update-db
update-db!: ; $(docker-compose-remote) run --rm wp core update-db
public-db!: ; $(docker-compose-remote) run --rm wp search-replace --report-changed-only https://$(APP_NAME).$(REMOTE_HOST) https://$(PUBLIC_HOST)

pull-db:
	$(docker-compose) run --rm env pull_db
	$(docker-compose) run --rm wp search-replace --report-changed-only https://$(PUBLIC_HOST) https://$(APP_NAME).$(APP_HOST)

push-db:
	$(docker-compose) run --rm env push_db
	$(docker-compose-remote) run --rm wp search-replace --report-changed-only https://$(APP_NAME).$(APP_HOST) https://$(PUBLIC_HOST)

pull-files: tmp-dir tmp-dir! 
	$(docker-compose-remote) run --rm env volume_to_host
	$(docker-compose) run --rm env remote_to_volume
	$(docker-compose-remote) run --rm env clean_host
	
push-files: tmp-dir tmp-dir!
	$(docker-compose) run --rm env volume_to_remote
	$(docker-compose-remote) run --rm env host_to_volume
	$(docker-compose-remote) run --rm env clean_host

import-files: ; $(docker-compose) run --rm env import_files
export-files: ; $(docker-compose) run --rm -e UID=$(shell id -u) env export_files

shell:  ; $(docker-compose) exec srv bash
shell!: ; $(docker-compose-remote) exec srv bash
host!:  ; ssh root@$(REMOTE_HOST)

push: push-files push-db
pull: pull-files pull-db
import: import-files import-db
export: export-files export-db

clean: ; rm -rf data/* && touch data/.gitkeep

restore: ; $(docker-compose) run --rm -e UID=$(shell id -u) env restore

install: .env tmp-dir assets rebuild
	$(docker-compose) run --rm wp core install \
		--url=https://$(APP_NAME).$(APP_HOST) \
		--title=$(APP_NAME) \
		--admin_email=web@nonfiction.ca \
		--admin_user=nf-$(APP_NAME) \
		--admin_password=$(DB_PASSWORD)
	$(docker-compose) run --rm wp plugin activate --all
	$(docker-compose) run --rm wp theme activate src
	$(docker-compose) run --rm wp rewrite structure /%postname%/
	@echo 
	@echo URL: https://$(APP_NAME).$(APP_HOST)/wp/wp-login.php
	@echo Username: nf-$(APP_NAME)
	@echo Password: $(DB_PASSWORD)

pull-images:
	docker image pull nonfiction/bedrock:env
	docker image pull nonfiction/bedrock:dev
	docker image pull nonfiction/bedrock:srv

pull-images!:
	ssh root@$(REMOTE_HOST) docker image pull nonfiction/bedrock:env
	ssh root@$(REMOTE_HOST) docker image pull nonfiction/bedrock:srv

.PHONY: bedrock
bedrock:   ; cd bedrock && docker-compose build && docker-compose push
env-shell: ; $(docker-compose) run --rm --entrypoint /bin/bash env
dev-shell: ; $(docker-compose-prod) run --rm --entrypoint /bin/bash dev
srv-shell: ; $(docker-compose-prod) run --rm --entrypoint /bin/bash srv

test: ; $(docker-compose) run --rm env test && ./cmd.sh
