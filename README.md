# Sentinel-raspi-site

Site web/API Symfony à installer dans un **Raspberry Pi 3B+**.


## Configuration

# Sentinel-raspi-site

Site web/API Symfony à installer dans un **Raspberry Pi 3B+**.


## Configuration

sudo apt update && sudo apt upgrade -y

# Install Apache2
sudo apt install -y apache2
~~sudo nano /etc/apache2/apache2.conf~~

# Install/Configure MariaDB
sudo apt install -y mariadb-server
sudo mysql_secure_installation
    enter
    n
    y
    Sentinel2025
    y
    y
    y
    y
sudo mysql -u root -p
    ~~CREATE DATABASE sentinel_db;~~
    CREATE USER 'sentinel'@'localhost' IDENTIFIED BY 'Sentinel2025';
    GRANT ALL PRIVILEGES ON sentinel_db.* TO 'sentinel'@'localhost';
    FLUSH PRIVILEGES;
    EXIT;


# To connect to database :
command : sudo mysql -u root -p
login : root
password : Sentinel2025



# Install/Configure PHP
sudo apt install -y  php php-cli php-mbstring php-curl php-mysql php-xml
sudo nano /etc/php/8.2/apache2/php.ini #uncomment extension cli mysqli mbstring fileinfo gd curl pdo_mysql


# Install Composer
curl -sS https://getcomposer.org/installer | php
if mv doesn't work do this command before : sudo mkdir -p /usr/local/bin
sudo mv composer.phar /usr/local/bin/composer

# Symfony
wget https://get.symfony.com/cli/installer -O - | bash
mv ~/.symfony5/bin/symfony /usr/local/bin/symfony

# Setup Website 
cd /var/www
rm -r html
sudo git clone https://forge.univ-lyon1.fr/WOT_BUT3WWW_2025/groupe-10/sentinel.git
cd sentinel
git config --global --add safe.directory /var/www/sentinel
cp .env .env.local
nano .env.local
    APP_DEBUG=0
    APP_ENV=prod
    DATABASE_URL="mysql://sentinel:Sentinel2025@127.0.0.1:3306/sentinel_db"

sudo chmod -R 775 /var/www/sentinel
mkdir -p /var/www/sentinel/vendor
composer install --no-dev --optimize-autoloader
php bin/console doctrine:database:create
mkdir -p migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console cache:clear

symfony server:ca:install
sudo npm i
sudo npm run build
sudo nano config/packages/web_profiler.yaml
mettre dans web_profiler.yaml  : 
    web_profiler: 
	    toolbar: false


# Start Website 
symfony server:start

# Configure Apache2
sudo nano /etc/apache2/sites-available/Sentinel.conf
    <VirtualHost *:80>
        ServerName sentinel.com
        DocumentRoot /var/www/sentinel/public

        <Directory /var/www/sentinel/public>
            AllowOverride All
            Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>

sudo a2ensite sentinel.conf
sudo a2enmod rewrite
sudo systemctl restart apache2

# Setup/Run acces point
sudo apt install -y hostapd dnsmasq
sudo systemctl unmask hostapd
sudo systemctl enable hostapd
sudo nano /etc/dhcpcd.conf
    interface wlan0
    static ip_address=192.168.4.1/24
    nohook wpa_supplicant
sudo systemctl restart dhcpcd

sudo mv /etc/dnsmasq.conf /etc/dnsmasq.conf.bak
sudo nano /etc/dnsmasq.conf
interface=wlan0
dhcp-range=192.168.4.1,192.168.4.100,255.255.255.0,24h


