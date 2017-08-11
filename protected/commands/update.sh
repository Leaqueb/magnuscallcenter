#!/bin/bash
clear
echo
echo
echo
echo "=======================WWW.MAGNUSCALLCENTER.COM===========================";
echo "_      _                               ______ _ _ _ _  			 ";
echo "|\    /|                               | ___ (_) | (_) 			 ";
echo "| \  / | ___  ____ _ __  _   _   _____ | |_/ /_| | |_ _ __   __ _ 	 ";
echo "|  \/  |/   \/  _ \| '_ \| | | \| ___| | ___ \ | | | | '_ \ /  _ \	 ";
echo "| |\/| |  | |  (_| | | | | |_| ||____  | |_/ / | | | | | | |  (_| |	 ";
echo "|_|  |_|\___|\___  |_| | |_____|_____|  \___/|_|_|_|_|_| |_|\___  |	 ";
echo "                _/ |                                           _/ |	 ";
echo "               |__/                                           |__/ 	 ";
echo "														 ";
echo "============================== UPDATE =================================";
echo

sleep 2

if [[ -e /var/www/html/callcenter/protected/commands/update2.sh ]]; then
	/var/www/html/callcenter/protected/commands/update2.sh
	exit;
fi
rm -rf /var/www/html/callcenter/Callcenter*.tar.gz
cd /var/www/html/callcenter
wget http://master.dl.sourceforge.net/project/magnuscallcenter/CallCenter-Update-3-current.tar.gz
tar xzf CallCenter-Update-3-current.tar.gz
rm -rf /var/www/html/callcenter/CallCenter*.tar.gz
php /var/www/html/callcenter/cron.php UpdateMysql
rm -rf /var/www/html/callcenter/doc
chown asterisk:asterisk /var/lib/asterisk/agi-bin/magnus
chown -R asterisk:asterisk /var/www/html/llamadas
chmod -R 750 /var/www/html/llamadas
chown -R asterisk:asterisk /var/spool/asterisk/
chown -R asterisk:asterisk /var/lib/php/session/
chown -R asterisk:asterisk /var/spool/asterisk/outgoing/
chown -R asterisk:asterisk /etc/asterisk
chown -R asterisk:asterisk /var/www/html/callcenter
chmod -R 777 /tmp
chmod -R 555 /var/www/html/callcenter/
chmod -R 750 /var/www/html/callcenter/resources/reports 
chmod -R 774 /var/www/html/callcenter/protected/runtime/
mkdir -p /usr/local/src/magnus/monitor
mkdir -p /usr/local/src/magnus/sounds
mkdir -p /usr/local/src/magnus/backup
mv /usr/local/src/backup* /usr/local/src/magnus/backup
chown -R asterisk:asterisk /usr/local/src/magnus/
chmod -R 755 /usr/local/src/magnus/
chmod 750 /var/www/html/callcenter/tmp
chmod 750 /var/www/html/mbilling/resources/sounds
chmod 770 /var/www/html/callcenter/resources/images
chown -R asterisk:asterisk /var/www/html/llamadas