#!/bin/bash
clear
echo
echo
echo
echo "=================================WWW.MAGNUSCALLCENTER.COM===================================";
echo "_      _                                ____      			                              ";
echo "|\    /|                               | ___|      _   _ 	                                  ";
echo "| \  / | ___  ____  _ __  _   _  _____ | |    ___ | | | |	  ___  ____ _ __  _____ ____ ___  ";
echo "|  \/  |/   \/  _ \| '_ \| | | \| ___| | |   /   \| | | |  / _ \| __ | '_ \|_  _|| __ | | \ ";
echo "| |\/| |  | |  (_| | | | | |_| ||____  | |__|  | || |_| |_| |_  |	__ | | | | | | | __ | _ / ";
echo "|_|  |_|\___|\___  |_| | |_____|_____|  \___|\___||___|___|\___||____|_| | | | | |____|  \  ";
echo "                _/ |                                           	                          ";
echo "               |__/                                            	                          ";
echo "																		                      ";
echo "============================ OPENSOURCE SYSTEM TO CALLCENTER ===============================";
echo

sleep 3


VERSION='7'


echo '[mariadb]
name = MariaDB
baseurl = http://yum.mariadb.org/10.1/centos7-amd64
gpgkey=https://yum.mariadb.org/RPM-GPG-KEY-MariaDB
gpgcheck=1' > /etc/yum.repos.d/MariaDB.repo 


sed 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config > borra && mv -f borra /etc/selinux/config
yum clean all
yum -y install kernel-devel.`uname -m` epel-release
yum -y install gcc.`uname -m` gcc-c++.`uname -m` make.`uname -m` git.`uname -m` wget.`uname -m` bison.`uname -m` openssl-devel.`uname -m` ncurses-devel.`uname -m` doxygen.`uname -m` newt-devel.`uname -m` mlocate.`uname -m` lynx.`uname -m` tar.`uname -m` wget.`uname -m` nmap.`uname -m` bzip2.`uname -m` mod_ssl.`uname -m` speex.`uname -m` speex-devel.`uname -m` unixODBC.`uname -m` unixODBC-devel.`uname -m` libtool-ltdl.`uname -m` sox libtool-ltdl-devel.`uname -m` flex.`uname -m` screen.`uname -m` autoconf automake libxml2.`uname -m` libxml2-devel.`uname -m` sqlite* subversion
yum -y install php.`uname -m` php-cli.`uname -m` php-devel.`uname -m` php-gd.`uname -m` php-mbstring.`uname -m` php-pdo.`uname -m` php-xml.`uname -m` php-xmlrpc.`uname -m` php-process.`uname -m` php-posix libuuid uuid uuid-devel libuuid-devel.`uname -m`
yum -y install jansson.`uname -m` jansson-devel.`uname -m` unzip.`uname -m`
yum -y install mysql mariadb-server  mariadb-devel mariadb php-mysql mysql-connector-odbc
yum -y install xmlstarlet libsrtp libsrtp-devel dmidecode gtk2-devel binutils-devel svn libtermcap-devel libtiff-devel audiofile-devel cronie cronie-anacron


systemctl enable httpd.service && systemctl enable mariadb

echo
echo '----------- Install Asterisk 14 ----------'
echo
sleep 1

cd /usr/src

wget http://www.digip.org/jansson/releases/jansson-2.7.tar.gz
tar -zxvf jansson-2.7.tar.gz
cd jansson-2*
./configure
make clean
make && make install
ldconfig

cd /usr/src
rm -rf asterisk*

clear
cd /usr/src
rm -rf asterisk*
clear
wget http://downloads.asterisk.org/pub/telephony/asterisk/asterisk-13-current.tar.gz
cd /usr/src
tar xzvf asterisk-13-current.tar.gz
rm -rf asterisk-13-current.tar.gz
cd asterisk-*
useradd -c 'Asterisk PBX' -d /var/lib/asterisk asterisk
mkdir /var/run/asterisk
mkdir /var/log/asterisk
chown -R asterisk:asterisk /var/run/asterisk
chown -R asterisk:asterisk /var/log/asterisk
make clean
./configure --with-ssl
contrib/scripts/install_prereq install
contrib/scripts/get_mp3_source.sh
make menuselect.makeopts
menuselect/menuselect --enable res_config_mysql  menuselect.makeopts
menuselect/menuselect --enable format_mp3  menuselect.makeopts
menuselect/menuselect --enable codec_opus  menuselect.makeopts
menuselect/menuselect --enable codec_silk  menuselect.makeopts
menuselect/menuselect --enable codec_siren7  menuselect.makeopts
menuselect/menuselect --enable codec_siren14  menuselect.makeopts
make
make install
make samples
make config
ldconfig


genpasswd() 
{
    length=$1
    [ "$length" == "" ] && length=16
    tr -dc A-Za-z0-9_ < /dev/urandom | head -c ${length} | xargs
}
password=$(genpasswd)

if [ -e "/root/passwordMysql.log" ] && [ ! -z "/root/passwordMysql.log" ]
then
    password=$(awk '{print $1}' /root/passwordMysql.log)
fi

touch /root/passwordMysql.log
echo "$password" > /root/passwordMysql.log 


clear
echo
echo "----------- Creat password mysql: Your mysql root password is $password ----------"
echo

chmod -R 777 /tmp
sleep 2
systemctl start mariadb
mysqladmin -u root password $password



echo "
<IfModule mod_deflate.c>
	AddOutputFilterByType DEFLATE text/plain
	AddOutputFilterByType DEFLATE text/html
	AddOutputFilterByType DEFLATE text/xml
	AddOutputFilterByType DEFLATE text/css
	AddOutputFilterByType DEFLATE text/javascript
	AddOutputFilterByType DEFLATE image/svg+xml
	AddOutputFilterByType DEFLATE image/x-icon
	AddOutputFilterByType DEFLATE application/xml
	AddOutputFilterByType DEFLATE application/xhtml+xml
	AddOutputFilterByType DEFLATE application/rss+xml
	AddOutputFilterByType DEFLATE application/javascript
	AddOutputFilterByType DEFLATE application/x-javascript
	DeflateCompressionLevel 9
	BrowserMatch ^Mozilla/4 gzip-only-text/html
	BrowserMatch ^Mozilla/4\.0[678] no-gzip
	BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
	BrowserMatch \bOpera !no-gzip
	DeflateFilterNote Input instream
	DeflateFilterNote Output outstream
	DeflateFilterNote Ratio ratio
	LogFormat '\"%r\" %{outstream}n/%{instream}n (%{ratio}n%%)' deflate
	CustomLog logs/deflate_log DEFLATE
</IfModule>
" >> /etc/httpd/conf.d/deflate.conf

echo "
<IfModule mod_expires.c>
 ExpiresActive On
 ExpiresByType image/jpg \"access plus 60 days\"
 ExpiresByType image/png \"access plus 60 days\"
 ExpiresByType image/gif \"access plus 60 days\"
 ExpiresByType image/jpeg \"access plus 60 days\"
 ExpiresByType text/css \"access plus 1 days\"
 ExpiresByType image/x-icon \"access plus 1 month\"
 ExpiresByType application/pdf \"access plus 1 month\"
 ExpiresByType audio/x-wav \"access plus 1 month\"
 ExpiresByType audio/mpeg \"access plus 1 month\"
 ExpiresByType video/mpeg \"access plus 1 month\"
 ExpiresByType video/mp4 \"access plus 1 month\"
 ExpiresByType video/quicktime \"access plus 1 month\"
 ExpiresByType video/x-ms-wmv \"access plus 1 month\"
 ExpiresByType application/x-shockwave-flash \"access 1 month\"
 ExpiresByType text/javascript \"access plus 1 week\"
 ExpiresByType application/x-javascript \"access plus 1 week\"
 ExpiresByType application/javascript \"access plus 1 week\"
</IfModule>
" >> /etc/httpd/conf.d/expire.conf

echo '<IfModule mime_module>
AddType application/octet-stream .csv
</IfModule>

<Directory "/var/www/html">
    DirectoryIndex index.htm index.html index.php index.php3 default.html index.cgi
</Directory>


<Directory "/var/www/html/callcenter/protected">
    deny from all
</Directory>

<Directory "/var/www/html/callcenter/yii">
    deny from all
</Directory>

<Directory "/var/www/html/callcenter/doc">
    deny from all
</Directory>

<Directory "/var/www/html/callcenter/resources/*log">
    deny from all
</Directory>

<Files "*.sql">
  deny from all
</Files>

<Files "*.log">
  deny from all
</Files>' >> /etc/httpd/conf/httpd.conf


sed -i "s/memory_limit = 16M/memory_limit = 512M /" /etc/php.ini
sed -i "s/memory_limit = 128M/memory_limit = 512M /" /etc/php.ini 
sed -i "s/upload_max_filesize = 2M/upload_max_filesize = 3M /" /etc/php.ini 
sed -i "s/post_max_size = 8M/post_max_size = 20M/" /etc/php.ini
sed -i "s/max_execution_time = 30/max_execution_time = 90/" /etc/php.ini
sed -i "s/max_input_time = 60/max_input_time = 120/" /etc/php.ini
sed -i "s/User apache/User asterisk/" /etc/httpd/conf/httpd.conf
sed -i "s/Group apache/Group asterisk/" /etc/httpd/conf/httpd.conf
sed -i "s/\;date.timezone =/date.timezone = America\/Sao_Paulo/" /etc/php.ini


systemctl restart  httpd


cd /usr/src
wget http://magnussolution.com/mpg123-1.20.1.tar.bz2
tar -xjvf mpg123-1.20.1.tar.bz2
cd mpg123-1.20.1
./configure && make && make install

clear
echo
echo '----------- Installing the Web Interface ----------'
echo
sleep 2
cd /var/www/html/
git clone https://github.com/magnussolution/magnuscallcenter.git callcenter
cd /var/www/html/callcenter

echo '----upload callcenter----'

chown -R asterisk:asterisk /var/www/html/callcenter
touch /etc/asterisk/extensions_magnus.conf
touch /etc/asterisk/sip_magnus_register.conf
touch /etc/asterisk/sip_magnus.conf
touch /etc/asterisk/sip_magnus_user.conf
touch /etc/asterisk/queue_magnus.conf
mkdir /var/run/magnus/
chown -R asterisk:asterisk /var/run/magnus/
cp -rf /var/www/html/callcenter/resources/sounds/br /var/lib/asterisk/sounds

language='br'
cd /var/lib/asterisk
wget https://sourceforge.net/projects/disc-os/files/Disc-OS%20Sounds/1.0-RELEASE/Disc-OS-Sounds-1.0-pt_BR.tar.gz
tar xzf Disc-OS-Sounds-1.0-pt_BR.tar.gz
rm -rf Disc-OS-Sounds-1.0-pt_BR.tar.gz

cp -n /var/lib/asterisk/sounds/pt_BR/*  /var/lib/asterisk/sounds/br
rm -rf /var/lib/asterisk/sounds/pt_BR
mkdir -p /var/lib/asterisk/sounds/br/digits
cp -rf /var/lib/asterisk/sounds/digits/pt_BR/* /var/lib/asterisk/sounds/br/digits
cp -n /var/www/html/mbilling/resources/sounds/br/* /var/lib/asterisk/sounds



echo "[magnuscallcenter]
exten => _X.,1,AGI("/var/www/html/callcenter/agi.php")

exten => 5555,1,Goto(spycall,\$\{EXTEN\},1)


[spycall];extension for spy customers
exten => 5555,1,NoOp(Escuta remota)
	same => n,Answer
	same => n,Authenticate(3003)
	same => n,WaitExten(5)

exten => _XXXXX.,1,ChanSpy(SIP/\$\{EXTEN\},bq)
	same =>n,Hangup()

[macro-queuemacro]
exten => s,1,AGI(magnus,queuemacro)

" > /etc/asterisk/extensions_magnus.conf



echo '
[general]
autofill=yes
shared_lastcall=yes
persistentmembers=yes
updatecdr=yes

#include queue_magnus.conf

' > /etc/asterisk/queues.conf


echo "
[general]
enabled = yes

port = 5038
bindaddr = 0.0.0.0

[magnus]
secret = magnussolution
deny=0.0.0.0/0.0.0.0
permit=127.0.0.1/255.255.255.0
read = system,call,log,verbose,agent,user,config,dtmf,reporting,cdr,dialplan
write = system,call,agent,user,config,command,reporting,originate
" > /etc/asterisk/manager.conf


echo "#include sip_magnus.conf" >> /etc/asterisk/sip.conf
echo "#include sip_magnus_user.conf" >> /etc/asterisk/sip.conf
echo "#include extensions_magnus.conf" >> /etc/asterisk/extensions.conf



echo
echo "----------- Installing the new Database ----------"
echo
sleep 2
CallCenterMysqlPass=$(genpasswd)
mysql -uroot -p${password} -e "create database callcenter;"
mysql -uroot -p${password} -e "CREATE USER 'CallCenterUser'@'localhost' IDENTIFIED BY '${CallCenterMysqlPass}';"
mysql -uroot -p${password} -e "GRANT ALL PRIVILEGES ON \`callcenter\` . * TO 'CallCenterUser'@'localhost' WITH GRANT OPTION;FLUSH PRIVILEGES;"    
mysql -uroot -p${password} -e "GRANT FILE ON * . * TO  'CallCenterUser'@'localhost' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;"

mysql callcenter -u root -p$password  < /var/www/html/callcenter/doc/script.sql


ln -s /var/www/html/callcenter/resources/scripts/AsteriskSoket/AsteriskSocket /etc/init.d/

cd /var/www/html/callcenter/resources/scripts/AsteriskSoket/
tar zxvf apps-sys-utils-start-stop-daemon-IR1_9_18-2.tar.gz
cd apps/sys-utils/start-stop-daemon-IR1_9_18-2
gcc start-stop-daemon.c -o start-stop-daemon
cp start-stop-daemon /usr/sbin/


cd /etc/init.d/
mv /etc/init.d/asterisk /tmp/asterisk_old
rm -rf /etc/init.d/asterisk
wget http://magnussolution.com/scriptsSh/asteriskCallCenter
mv asteriskCallCenter asterisk
chmod +x /etc/init.d/asterisk
systemctl daemon-reload

echo "
4 4 * * * php /var/www/html/callcenter/cron.php CallArchive
55 3 * * * php /var/www/html/callcenter/cron.php payments
50 23 * * * php /var/www/html/callcenter/cron.php asistenciackeck
* * * * * php /var/www/html/callcenter/cron.php TurnosCkeck
* * * * * php /var/www/html/callcenter/cron.php Category
* * * * * php /var/www/html/callcenter/cron.php predictive
30 23 * * * php /var/www/html/callcenter/cron.php backup
1 1 * * * /usr/sbin/ntpdate ntp.ubuntu.com pool.ntp.org
#0 3 * * * /var/www/html/callcenter/protected/commands/update.sh
0 4 * * * /var/www/html/callcenter/protected/commands/verificamemoria
" > /var/spool/cron/root


echo "
[general]
bindaddr=0.0.0.0
bindport=5060
context = billing
dtmfmode=RFC2833
disallow=all
allow=g729
allow=g723
allow=ulaw  
allow=alaw  
allow=gsm
rtcachefriends=yes
srvlookup=yes
alwaysauthreject=yes
rtupdate=yes
callcounter=yes
allowsubscribe=yes 
subscribecontext=subscribe
notifyringing=yes
notifyhold=yes

#include sip_magnus_register.conf
#include sip_magnus_user.conf
#include sip_magnus.conf
" > /etc/asterisk/sip.conf

echo "[general]
dbhost = 127.0.0.1
dbname = callcenter
dbuser = CallCenterUser
dbpass = ${CallCenterMysqlPass}
" > /etc/asterisk/res_config_mysql.conf


echo "<?php 
header('Location: ./callcenter');
?>
" > /var/www/html/index.php

echo "
User-agent: *
Disallow: /callcenter/
" > /var/www/html/robots.txt


yum install -y epel-release
yum install -y iptables-services

yum install -y iptables-services
rm -rf /etc/fail2ban
cd /tmp
git clone https://github.com/fail2ban/fail2ban.git
cd /tmp/fail2ban
python setup.py install

systemctl mask firewalld.service
systemctl enable iptables.service
systemctl enable ip6tables.service
systemctl stop firewalld.service
systemctl start iptables.service
systemctl start ip6tables.service

systemctl enable iptables

chkconfig --levels 123456 firewalld off


iptables -F
iptables -A INPUT -p icmp --icmp-type echo-request -j ACCEPT
iptables -A OUTPUT -p icmp --icmp-type echo-reply -j ACCEPT
iptables -A INPUT -i lo -j ACCEPT
iptables -A INPUT -m state --state ESTABLISHED,RELATED -j ACCEPT
iptables -A INPUT -p tcp --dport 22 -j ACCEPT
iptables -P INPUT DROP
iptables -P FORWARD DROP
iptables -P OUTPUT ACCEPT
iptables -A INPUT -p udp -m udp --dport 5060 -j ACCEPT
iptables -A INPUT -p udp -m udp --dport 10000:20000 -j ACCEPT
iptables -A INPUT -p tcp -m tcp --dport 80 -j ACCEPT
iptables -I INPUT -j DROP -p udp --dport 5060 -m string --string "friendly-scanner" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5060 -m string --string "sundayddr" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5060 -m string --string "sipsak" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5060 -m string --string "sipvicious" --algo bm
iptables -I INPUT -j DROP -p udp --dport 5060 -m string --string "iWar" --algo bm
iptables -A INPUT -j DROP -p udp --dport 5060 -m string --string "sipcli/" --algo bm
iptables -A INPUT -j DROP -p udp --dport 5060 -m string --string "VaxSIPUserAgent/" --algo bm

service iptables save
service iptables restart


echo
echo "Fail2ban configuration!"
echo

echo '
Defaults!/usr/bin/fail2ban-client !requiretty
asterisk ALL=(ALL) NOPASSWD: /usr/bin/fail2ban-client
' >> /etc/sudoers


echo '
[INCLUDES]
[Definition]
failregex = NOTICE.* .*: Useragent: sipcli.*\[<HOST>\] 
ignoreregex =
' > /etc/fail2ban/filter.d/asterisk_cli.conf

echo '
[INCLUDES]
[Definition]
failregex = .*NOTICE.* <HOST> tried to authenticate with nonexistent user.*
ignoreregex =
' > /etc/fail2ban/filter.d/asterisk_manager.conf

echo '
[INCLUDES]
[Definition]
failregex = NOTICE.* .*hangupcause to DB: 200, \[<HOST>\]
ignoreregex =
' > /etc/fail2ban/filter.d/asterisk_hgc_200.conf



echo "
[DEFAULT]
ignoreip = 127.0.0.1
bantime  = 600
findtime  = 600
maxretry = 3
backend = auto
usedns = warn


[asterisk-iptables]   
enabled  = true           
filter   = asterisk       
action   = iptables-allports[name=ASTERISK, port=5060, protocol=all]   
logpath  = /var/log/asterisk/messages 
maxretry = 5  
bantime = 600

[ast-cli-attck]   
enabled  = true           
filter   = asterisk_cli     
action   = iptables-allports[name=AST_CLI_Attack, port=5060, protocol=all]
logpath  = /var/log/asterisk/messages 
maxretry = 1  
bantime = -1

[asterisk-manager]   
enabled  = true           
filter   = asterisk_manager     
action   = iptables-allports[name=AST_MANAGER, port=5038, protocol=all]
logpath  = /var/log/asterisk/messages 
maxretry = 1  
bantime = -1

[ast-hgc-200]
enabled  = true           
filter   = asterisk_hgc_200     
action   = iptables-allports[name=AST_HGC_200, port=5060, protocol=all]
logpath  = /var/log/asterisk/messages
maxretry = 20
bantime = -1

[ssh-iptables]
enabled  = true
filter   = sshd
action   = iptables-allports[name=SSH, port=all, protocol=all]
logpath  = /var/log/secure
maxretry = 3
bantime = 600

" > /etc/fail2ban/jail.local



echo "
[general]
dateformat=%F %T       ; ISO 8601 date format
[logfiles]

;debug => debug
;security => security
console => warning,error
;console => notice,warning,error,debug
messages => notice,warning,error
;full => notice,warning,error,debug,verbose,dtmf,fax

fail2ban => notice
" > /etc/asterisk/logger.conf

mkdir /var/run/fail2ban/
asterisk -rx "module reload logger"
systemctl enable fail2ban 
systemctl restart fail2ban 
iptables -L -v


cd /usr/local/sbin
wget magnussolution.com/download/sip
chmod 777 /usr/local/sbin/*

yum install -y ngrep htop ntp


rm -f /etc/localtime
ln -s /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime

ntpdate pool.ntp.org
hwclock --systohc

php /var/www/html/callcenter/cron.php updatemysql

chown asterisk:asterisk /var/lib/asterisk/agi-bin/magnus
chmod +x /var/www/html/callcenter/resources/asterisk/magnus.php
chown -R asterisk:asterisk /var/spool/asterisk/monitor
chmod -R 750 /var/spool/asterisk/monitor
chown -R asterisk:asterisk /var/spool/asterisk/
chown -R asterisk:asterisk /var/lib/php/session/
chown -R asterisk:asterisk /var/spool/asterisk/outgoing/
chown -R asterisk:asterisk /etc/asterisk
chown -R asterisk:asterisk /var/www/html/callcenter
chmod +x /var/www/html/callcenter/agi.php
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
chmod 750 /var/www/html/callcenter/resources/sounds
chmod 770 /var/www/html/callcenter/resources/images
rm -rf /var/www/html/callcenter/doc

echo
echo
echo ===============================================================
echo 
echo Congratulations! You have installed Magnus CallCenter Server.
echo
echo Please reboot your server.
echo
echo Access your MagnusCallCenter in http://your_ip/
echo Username = admin
echo Passwor = magnus
echo
echo Your mysql root password is $password
echo 
echo ===============================================================
echo
sleep 4
reboot