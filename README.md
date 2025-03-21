# Sentinel-raspi-site

Site web/API Symfony à installer dans un **Raspberry Pi 3B+**.


## Configuration

# Sentinel-raspi-site

Site web/API Symfony à installer dans un **Raspberry Pi 3B+**.


## Configuration

sudo apt update && sudo apt upgrade -y

# Install/Configure Apache2
sudo apt install -y apache2
sudo nano /etc/apache2/apache2.conf

# Install/Configure MariaDB
sudo apt install -y mariadb-server
sudo mysql_secure_installation

# Install/Configure PHP
sudo apt install -y  php php-cli php-mbstring php-curl php-mysql
sudo nano /etc/php/8.2/apache2/php.ini

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Symfony
wget https://get.symfony.com/cli/installer | bash
sudo mv ~/.symfony/bin/symfony /usr/local/bin/symfony

# Setup Website 
cd /var/www
sudo git clone https://forge.univ-lyon1.fr/WOT_BUT3WWW_2025/groupe-10/sentinel.git
cd /var/www/Sentinel
sudo cp .env .env.local
sudo cp .env .env.local
sudo nano .env.local
    APP_DEBUG=0
    APP_ENV=prod
    DATABASE_URL=mysql://ocpeps_user:ocpepsUSER!794!@127.0.0.1:3306/ocpeps

sudo npm i
sudo npm run build
sudo nano config/packages/web_profiler.yaml
mettre dans web_profiler.yaml  : 
    web_profiler: 
	    toolbar: false


symfony server:ca:install

# Start Website 
symfony:server:start

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


