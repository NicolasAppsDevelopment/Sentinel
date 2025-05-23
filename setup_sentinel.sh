#!/bin/bash

# Update and upgrade
sudo apt update
sudo apt upgrade -y

# Install required packages
sudo apt install -y ca-certificates curl

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
sudo apt update
sudo apt upgrade -y

# Install Docker
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker

# Host-container communication pipe for bash commands
cat > /usr/local/bin/host_listener.sh <<EOF
#!/bin/bash

sudo rm -rf /tmp/host_command_pipe
mkfifo /tmp/host_command_pipe
chmod 666 /tmp/host_command_pipe

while true; do
  if read -r cmd < /tmp/host_command_pipe; then
    echo "Executing: \$cmd"
    bash -c "\$cmd"
  fi
done
EOF
sudo chmod +x /usr/local/bin/host_listener.sh
cat > /etc/systemd/system/host_listener.service <<EOF
[Unit]
Description=Host Command Listener for Docker Container
After=network.target

[Service]
Type=simple
ExecStart=/usr/local/bin/host_listener.sh
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF
sudo systemctl daemon-reload
sudo systemctl enable host_listener.service
sudo systemctl start host_listener.service

# Clone the repo and start Docker Compose
git clone https://github.com/NicolasAppsDevelopment/Sentinel.git
cd sentinel
docker compose up -d

#Setup Acces Point
sudo nmcli con add con-name hotspot ifname wlan0 type wifi ssid "Sentinel" #access point name
sudo nmcli con modify hotspot wifi-sec.key-mgmt wpa-psk
sudo nmcli con modify hotspot wifi-sec.psk "Sentinel2025" #access point password
sudo nmcli con modify hotspot 802-11-wireless.mode ap 802-11-wireless.band bg ipv4.method shared

#Unblock the wlan0 if there is a lack of power
sudo rfkill unblock all
