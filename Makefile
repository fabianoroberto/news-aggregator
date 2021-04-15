#!make

include .env
-include .env.local
-include `.env.${APP_ENV}.local`
export

help: ## Show command list
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF}' $(MAKEFILE_LIST)

jwt: ## Create JWT private and public keys
	mkdir -p config/jwt
	openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:${JWT_PASSPHRASE}
	openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:${JWT_PASSPHRASE}

dump_env:
	@composer dump-env ${APP_ENV}

create_db: ## Create DB
	@bash dev/make/create_db.sh ${APP_ENV}

reset_db: ## drop db, create db, update schema and load fixtures
	@bash dev/make/reset_db.sh ${APP_ENV}

update_schema: ## Update DB Schema
	@bash dev/make/update_schema.sh ${APP_ENV}

refresh: ## Refresh cache
	@php bin/console cache:clear --env=${APP_ENV}