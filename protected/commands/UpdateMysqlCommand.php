<?php
class UpdateMysqlCommand extends ConsoleCommand
{
    public $config;
    public $success;

    public function run($args)
    {

        $version = $this->config['global']['version'];

        echo $version;

        if ($version == '3.0.0') {

            $sql = "ALTER TABLE  `pkg_campaign` ADD  `call_limit` INT( 11 ) NOT NULL DEFAULT  '0',
                            ADD  `call_next_try` INT( 11 ) NOT NULL DEFAULT  '30',
                            ADD  `predictive` INT( 11 ) NOT NULL DEFAULT  '0';
                    ALTER TABLE `pkg_breaks` CHANGE `start_time` `start_time` TIME NOT NULL DEFAULT '00:00:00';
                    ALTER TABLE `pkg_breaks` CHANGE `stop_time` `stop_time` TIME NOT NULL DEFAULT '00:00:00';
                    ALTER TABLE  `pkg_phonenumber` ADD  `cpf` VARCHAR( 15 ) NOT NULL DEFAULT  '' AFTER  `dni`;
            ";
            $this->executeDB($sql);

            $version = '3.0.1';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.1') {

            $sql = "ALTER TABLE  `pkg_campaign` ADD  `allow_neighborhood` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `allow_city`;
            ALTER TABLE  `pkg_phonenumber` ADD  `neighborhood` VARCHAR( 50 ) NOT NULL DEFAULT  '' AFTER  `city`;
            ALTER TABLE  `pkg_phonenumber` ADD  `try` INT( 1 ) NOT NULL DEFAULT  '0';
            ";
            $this->executeDB($sql);

            $version = '3.0.2';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }
        if ($version == '3.0.2') {

            $sql = "INSERT INTO pkg_configuration VALUES (NULL, 'Tolerancia para mais e para menos para pausas obrigatorias', 'break_tolerance', '3', 'Tolerancia para mais e para menos para pausas obrigatorias', 'global', '1');;
            ";
            $this->executeDB($sql);

            $version = '3.0.3';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.3') {

            $sql = "ALTER TABLE `pkg_logins_campaign` ADD CONSTRAINT `fk_pkg_logins_campaig_pkg_breaks` FOREIGN KEY (`id_breaks`) REFERENCES `pkg_breaks` (`id`);
            ALTER TABLE  `pkg_breaks` ADD  `status` TINYINT( 1 ) NOT NULL DEFAULT  '1'
            ";
            $this->executeDB($sql);

            $version = '3.0.4';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

    }

    private function executeDB($sql)
    {
        try {
            Yii::app()->db->createCommand($sql)->execute();
        } catch (Exception $e) {

        }
    }
}
