# Présentation
This Readme explain how to setup this project on a **Raspberry Pi 3B+**.

# Missing elements
Raspi setup missing to handle correctly wifi and Ethernet.
Sentinel AP config part not finished.

# Requirement for setup

## WIFI
Set the Wifi country :
- Execute `sudo raspi-config`
- Go in « Localisation Options » then « WLAN country »
- Select your country.
- Confirm and quit.

Connect the Raspberry Pi to your router with an Ethernet cable.

## Mail
Config the mail server by providing the SMTP server, the port, the login and the password into the file `php/ssmtp.conf`.

## Update
If the Raspberry Pi propose an update, do it.

## Script for setup
If you use ssh for this step don't forget to **enable ssh in** the configuration of the Raspberry Pi : 
`sudo raspi-config` -> "Interface Options" -> "SSH" -> Enable -> Yes -> OK

### Here the script and how to execute it :
`sudo nano setup_sentinel.sh`
Add the content of "setup_sentinel.sh" (in git) into the file in the Raspberry Pi

Execute the script :
`sudo bash setup_sentinel.sh`

# Note :
At one moment you will be in the root user. You must type "exit".
After that, you will be ask to enter you login and password for the git clone
