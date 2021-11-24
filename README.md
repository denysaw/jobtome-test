# URL Shortener and Counter
PHP test task for [Jobtome](https://jobtome.com/)

Stack: PHP 7.4, Symfony 5, Doctrine MongoDB ODM

## Task description:
Could be found [here](TASK.md)

## Prerequisites:
Free 80 port on localhost

## How to run:
```bash
docker-compose up -d
```
and check SwaggerUI at: `http://localhost/api/doc`

## How to run tests:
```bash
docker-compose exec php bash -c "./vendor/bin/phpunit"
```

P.S. I would also add Redis caching, request slowers/limiters, describe all 4xx errors in OpenAPI, but that would take more time :)
