# Article Aggregator

> Article aggregator build with Symfony 5.2

# How to start

```bash
docker-composer up -d
```

#Useful commands

## Create JWT private and public keys
```bash
mkdir -p config/jwt
openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:${JWT_PASSPHRASE}
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:${JWT_PASSPHRASE}
```

## Dump ENV
```bash
symfony composer dump-env ${APP_ENV}
```

## Create DB
```bash
symfony console doctrine:database:create
```

## Reset DB: (drop db, create db, update schema and load fixtures)
```bash
symfony console doctrine:database:drop --force
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate --no-interaction
symfony console doctrine:fixtures:load --no-interaction
```

## Update DB Schema
```bash
symfony console doctrine:migrations:migrate --no-interaction
```

## Refresh cache
```bash
symfony console cache:clear
```

# Fixtures

Load:

* 10 users (someone in soft-delete)
* 1 admin
* 10 articles
* 50 comments