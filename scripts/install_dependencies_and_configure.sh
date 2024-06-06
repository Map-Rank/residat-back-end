#!/bin/bash
cd /var/www/html/residat-back-end
sudo composer update --no-interaction
sudo composer install --no-interaction
sudo npm install
sudo chmod 777 -R storage/
sudo chmod 777 -R bootstrap/
sudo chmod 777 -R vendor/
sudo php artisan key:generate

