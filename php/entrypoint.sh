#!/bin/sh

# If the vendor directory doesn't exist or you need to ensure it's up to date,
# you can run composer install here.
cd /var/www/app-site
composer install 

# set permission for symfony
chmod -R 775 /var/www/app-site
chown -R www-data:www-data /var/www/app-site
chmod -R 775 /camera_pictures
chown -R www-data:www-data /camera_pictures

# Detect system timezone from /etc/timezone or /etc/localtime symlink
if [ -f /etc/timezone ]; then
    TZ=$(cat /etc/timezone)
elif [ -L /etc/localtime ]; then
    TZ=$(readlink /etc/localtime | sed 's|/usr/share/zoneinfo/||')
else
    TZ="UTC"
fi

echo "Detected timezone: $TZ"

# Apply it to php.ini
PHP_INI_PATH=/usr/local/etc/php/conf.d/confphp.ini
if [ -n "$PHP_INI_PATH" ]; then
    echo "Setting date.timezone in $PHP_INI_PATH"
    sed -i "s|^;*date.timezone =.*|date.timezone = \"$TZ\"|g" "$PHP_INI_PATH"
else
    echo "Could not locate php.ini"
fi

# wait MariaDB to be ready
until mariadb -h "$MYSQL_HOST" -u "$MYSQL_USER" --password="$MYSQL_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
  >&2 echo "Database is unavailable - waiting to be started..."
  sleep 5
done

php bin/console d:m:m
php bin/console asset-map:compile

exec "$@"
