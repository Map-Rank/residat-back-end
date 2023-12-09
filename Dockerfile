# Utilisez l'image PHP officielle avec Apache
FROM php:8.2-apache

# Définir les arguments d'environnement
ARG WWWUSER
ARG WWWGROUP

# Définir l'environnement
ENV APACHE_RUN_USER $WWWUSER
ENV APACHE_RUN_GROUP $WWWGROUP

# ... (autres instructions Docker)

# Installer Node.js et npm
RUN apt-get update && \
    apt-get install -y nodejs npm && \
    rm -rf /var/lib/apt/lists/*

# Copier les fichiers package.json et package-lock.json
COPY package*.json /var/www/html/

# Installer les dépendances Node.js
RUN npm install

# Copier le reste des fichiers du projet
COPY . /var/www/html/

# Exécuter les tâches de compilation (par exemple, npm run dev)
RUN npm run dev

# ... (autres instructions Docker)

# Définir le répertoire de travail
WORKDIR /var/www/html

# ... (autres instructions Docker)