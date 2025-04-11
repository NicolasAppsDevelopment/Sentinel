#!/bin/bash

# Update and upgrade
sudo apt-get update
sudo apt-get upgrade -y

# Install required packages
sudo apt-get install -y ca-certificates curl

# Create keyrings directory
sudo install -m 0755 -d /etc/apt/keyrings

# Download Docker GPG key
sudo curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

# Add Docker repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  tee /etc/apt/sources.list.d/docker.list > /dev/null

# Update again and upgrade
sudo apt-get update
sudo apt-get upgrade -y

# Install Docker
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin 

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker

# Clone the repo and start Docker Compose
git clone https://forge.univ-lyon1.fr/WOT_BUT3WWW_2025/groupe-10/sentinel.git
cd sentinel
docker compose up -d


#Loop to wait for docker container to be ready
until curl -s -o /dev/null -w "%{http_code}" http://localhost:8081/login | grep -q "200"
do
  echo "Waiting for the web site to be setup and ready to be used"
  sleep 5
done


# Configure static IP for wlan0
cat <<EOF > /etc/systemd/network/10-wlan0.network
[Match]
Name=wlan0

[Network]
Address=192.168.4.1/24
DHCPServer=yes
EOF

# Enable and restart systemd-networkd
systemctl enable systemd-networkd
systemctl restart systemd-networkd

# Install WiFi AP services
sudo apt install -y hostapd dnsmasq

# Disable NetworkManager
systemctl disable NetworkManager
systemctl stop NetworkManager

# Create hostapd config
cat <<EOF > /etc/hostapd/hostapd.conf
interface=wlan0
driver=nl80211
ssid=Sentinel
hw_mode=g
channel=7
wmm_enabled=0
macaddr_acl=0
auth_algs=1
ignore_broadcast_ssid=0
wpa=2
wpa_passphrase=Sentinel2025
wpa_key_mgmt=WPA-PSK
rsn_pairwise=CCMP
EOF

# Update hostapd default config
sed -i 's|#DAEMON_CONF="".*|DAEMON_CONF="/etc/hostapd/hostapd.conf"|' /etc/default/hostapd

# Enable and start hostapd
systemctl unmask hostapd
systemctl enable hostapd
systemctl start hostapd

# Backup and create dnsmasq config
mv /etc/dnsmasq.conf /etc/dnsmasq.conf.bak

#Note you will be ask a question, answer it with Y
cat <<EOF > /etc/dnsmasq.conf
interface=wlan0
bind-interfaces
dhcp-range=192.168.4.2,192.168.4.20,255.255.255.0,24h
EOF

# Enable and start dnsmasq
systemctl enable dnsmasq
systemctl start dnsmasq

# Restart all services
systemctl restart systemd-networkd
systemctl restart hostapd
systemctl restart dnsmasq

# Final reboot
reboot
