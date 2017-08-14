#!/bin/bash
clear
echo
echo
echo
echo "=======================WWW.MAGNUSBILLING.COM===========================";
echo "_      _                               ______ _ _ _ _  			     ";
echo "|\    /|                               | ___ (_) | (_) 			     ";
echo "| \  / | ___  ____ _ __  _   _   _____ | |_/ /_| | |_ _ __   __ _ 	 ";
echo "|  \/  |/   \/  _ \| '_ \| | | \| ___| | ___ \ | | | | '_ \ /  _ \	 ";
echo "| |\/| |  | |  (_| | | | | |_| ||____  | |_/ / | | | | | | |  (_| |	 ";
echo "|_|  |_|\___|\___  |_| | |_____|_____|  \___/|_|_|_|_|_| |_|\___  |	 ";
echo "                _/ |                                           _/ |	 ";
echo "               |__/                                           |__/ 	 ";
echo "														                 ";
echo "============================== UPDATE =================================";
echo

sleep 2

if [[ -e /var/www/html/callcenter/protected/commands/update2.sh ]]; then
	/var/www/html/callcenter/protected/commands/update2.sh
	exit;
fi

cd /var/www/html/callcenter
## pull remote git repository
git fetch --all
git reset --hard origin/master
git fetch origin master
git clean -f -d
git pull

##update database
php /var/www/html/callcenter/cron.php UpdateMysql

## remove unnecessary directories
rm -rf /var/www/html/callcenter/doc
rm -rf /var/www/html/callcenter/script

## set default permissions 
chown -R asterisk:asterisk /var/lib/php/session/
chown -R asterisk:asterisk /var/spool/asterisk/outgoing/
chown -R asterisk:asterisk /etc/asterisk
chown -R asterisk:asterisk /var/www/html/callcenter
chmod -R 777 /tmp
chmod -R 555 /var/www/html/callcenter/
chmod -R 750 /var/www/html/callcenter/resources/reports 
chmod -R 774 /var/www/html/callcenter/protected/runtime/
chmod +x /var/www/html/callcenter/agi.php
mkdir -p /usr/local/src/magnus
chmod -R 755 /usr/local/src/magnus
chmod 774 /var/www/html/callcenter/resources/ip.blacklist
chmod -R 655 /var/www/html/callcenter/tmp
chmod -R 750 /var/www/html/callcenter/resources/sounds
chmod -R 770 /var/www/html/callcenter/resources/images