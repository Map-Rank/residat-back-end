## Deploiement de l'application

1. Cloner le projet

2. copy .\.env.example .env

3. php artisan key:generate

##fi

## Configuration du fichier .env

1. Configiration des variables
- DB_DATABASE=
- DB_USERNAME=root (default)
- DB_PASSWORD=
- APP_URL=http://localhost:8000

---- Ajouter ces variables a la fin du .env ----
- WWWUSER=1000
- WWWGROUP=1000
- PWD=c/laragon/www/residat-back-end

## Configurer docker

1. Installation de Docker

2. Redemarer le PC

## Invite de commande 
- Se placer dans le repertoire du projet
- saisir la commande 
" docker-compose up -d "