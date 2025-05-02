# Présentation
This Readme explain how to setup this project on a **Raspberry Pi 3B+**.

# Requirement for setup

## WIFI
Set the Wifi country 
Connect the Raspberry Pi to a Wifi

## Update
If the Raspberry Pi propose an update, do it.

## Script for setup
If you use ssh for this step don't forget to **enable ssh in** the configuration of the Raspberry Pi : 
`sudo raspi-config` -> "Interface Opiions" -> "SSH" -> Enable -> Yes -> OK

Be in root user to do the script : `su -`

### Here the script and how to execute it :
`sudo nano setup_sentinel.sh`
Add the content of "setup_sentinel.sh" (in git) into the file in the Raspberry Pi

Execute the script :
`sudo bash setup_sentinel.sh`

# Note :
You will be ask to enter you login and password for the git clone