# Présentation
This Readme explain how to setup this project on a **Raspberry Pi 3B+**.

# Requirement for setup

## WIFI

Set the Wifi country :
- Execute `sudo raspi-config`
- Go in « Localisation Options » then « WLAN country »
- Select your country.
- Confirm and quit.

Connect the Raspberry Pi to your router with an Ethernet cable.

## Update
If the Raspberry Pi propose an update, do it.

## Script for setup
If you use ssh for this step don't forget to **enable ssh** in the configuration of the Raspberry Pi : 
`sudo raspi-config` -> "Interface Opiions" -> "SSH" -> Enable -> Yes -> OK

### Here the script and how to execute it :
`sudo nano setup_sentinel.sh`
Add the content of "setup_sentinel.sh" (in git) into the file in the Raspberry Pi

Execute the script :
`sudo bash setup_sentinel.sh`

# Note :
At one moment you will be in the root user. You must type "exit".
After that, you will be ask to enter you login and password for the git clone

To access the website, type “ip a” and then search for the ip for eth0 (if you are connected on your router's wifi connection) or wlan0 (if you are connected on the Raspberry Pi's access point).
While connected to the raspberry Pi access point, type the url “the ip you found:8081”.

In order to use the action or camera modules, connect them to the Raspberry Pi's access point.
