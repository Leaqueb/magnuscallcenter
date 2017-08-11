<?php
class BackupCommand extends ConsoleCommand
{

    public function run($args)
    {
        $dbString = explode('dbname=', Yii::app()->db->connectionString);
        $dataBase = end($dbString);

        $username = Yii::app()->db->username;
        $password = Yii::app()->db->password;

        $comando = "/usr/bin/mysqldump -u" . $username . " -p" . $password . " " . $base . " > /tmp/base.sql";

        LinuxAccess::exec('find ' . $this->config['global']['record_patch'] . ' -size -30k -delete');
        LinuxAccess::exec($comando);

        $comando = "tar czvf /usr/local/src/backup_voip_Magnus.$data.tgz /tmp/base.sql /etc/asterisk " . $this->config['global']['record_patch'] . "/" . date('dmY');
        LinuxAccess::exec($comando);
        LinuxAccess::exec("rm -f /tmp/base.sql");
    }
}
