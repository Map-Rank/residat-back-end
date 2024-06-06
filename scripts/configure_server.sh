#!/bin/bash

cd /var/www/html/residat-back-end
sudo cp scripts/templates/000-default.conf /etc/apache2/sites-available/000-default.conf
sudo cp scripts/templates/dir.conf /etc/apache2/mods-enabled/dir.conf
sudo cp scripts/templates/apache2.conf /etc/apache2/apache2.conf
sudo cp scripts/templates/php.ini /etc/php/8.1/apache2/php.ini
sudo systemctl restart apache2

### Check if directory of laravel supervisor exist ###
if [ -d "/etc/supervisor/conf.d" ]
then
    # add the supervisor config
    sudo cp scripts/templates/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

    # read the new config
    sudo supervisorctl reread

    # activate our configuration
    sudo supervisorctl update

    # start queue command
    sudo supervisorctl start laravel-worker:*

    # check the status of our new config
    sudo supervisorctl status

    # schedule in cron tab
    crontab -r
    crontab /var/www/html/residat-back-end/scripts/templates/residat-back-end_cron.txt

fi


