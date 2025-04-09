#!/bin/sh
set -e

# If the vendor directory doesn't exist or you need to ensure it's up to date,
# you can run composer install here.
cd /var/www/app-site
composer install 

# set permision for symfony
chmod -R 775 /var/www/app-site
chown -R www-data:www-data /var/www/app-site
chmod -R 775 /camera_picture
chown -R www-data:www-data /camera_picture

# set symbolic link for image upload folder
if [ -d "/var/www/app-site/assets/camera_picture" ]; then
    rm -R /var/www/app-site/assets/camera_picture
fi
ln -s /camera_picture /var/www/app-site/assets/camera_picture

php bin/console d:m:m
php bin/console asset-map:compile

exec "$@"
