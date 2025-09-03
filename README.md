Este projeto utiliza **Docker Compose** para configurar e executar um ambiente Laravel.  


## Pré-requisitos
- Docker
- Docker Compose


### Copie e cole os comandos abaixo no seu terminal
```bash

#Cria os containers
docker compose build --no-cache

#Sobe os containers
docker compose up -d

#Instala o projeto
docker exec -it laravel_app composer install

docker exec -it laravel_app cp .env.example .env

docker exec -it laravel_app touch database/database.sqlite

docker exec -it laravel_app php artisan migrate

docker exec -it laravel_app php artisan db:seed

#Para rodar os testes
docker exec -it laravel_app php ./vendor/bin/phpunit 
