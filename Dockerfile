# Utilisez l'image PHP officielle avec Apache
FROM php:8.2-apache

# Définir les arguments d'environnement
ARG WWWUSER
ARG WWWGROUP

# Définir l'environnement
ENV APACHE_RUN_USER $WWWUSER
ENV APACHE_RUN_GROUP $WWWGROUP

# ... (autres instructions Docker)

# Créer le groupe défini dans les arguments
RUN groupadd --force -g $WWWGROUP $WWWGROUP

# ... (autres instructions Docker)

# Définir le répertoire de travail
WORKDIR /var/www/html

# ... (autres instructions Docker)