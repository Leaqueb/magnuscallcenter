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

        if ($version == '3.0.4') {

            $sql = "UPDATE `pkg_configuration` SET `config_description` = '1 to active, 0 to inactive ' WHERE config_key = 'amd';

            UPDATE `pkg_configuration` SET `status` = '0';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'base_language';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'version';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'admin_email';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'portabilidadeUsername';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'portabilidadePassword';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'operator_next_try';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'updateAll';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'campaign_limit';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'tardanza';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'valor_colectivo';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'valor_hora_zero';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'valor_hora';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'valor_falta';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'notify_url_after_save_number';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'notify_url_category';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'record_call';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'dialcommand_param';
            UPDATE `pkg_configuration` SET `status` = '1' WHERE config_key = 'MixMonitor_format';
            ALTER TABLE `pkg_category` ADD `color` VARCHAR(7) NOT NULL DEFAULT '#ffffff' AFTER `use_in_efetiva`;



            UPDATE `pkg_category` SET `color` = '#FF0000' WHERE id = 0;
            UPDATE `pkg_category` SET `color` = '#339966' WHERE id = 1;
            UPDATE `pkg_category` SET `color` = '#ddb96d' WHERE id = 2;
            UPDATE `pkg_category` SET `color` = '#FF99CC' WHERE id = 3;
            UPDATE `pkg_category` SET `color` = '#ab6b40' WHERE id = 4;
            UPDATE `pkg_category` SET `color` = '#800080' WHERE id = 5;
            UPDATE `pkg_category` SET `color` = '#00FF00' WHERE id = 6;
            UPDATE `pkg_category` SET `color` = '#d9d1a8' WHERE id = 7;
            UPDATE `pkg_category` SET `color` = '#8d5ed5' WHERE id = 8;
            UPDATE `pkg_category` SET `color` = '#993366' WHERE id = 9;
            UPDATE `pkg_category` SET `color` = '#FF0000' WHERE id = 10;
            UPDATE `pkg_category` SET `color` = '#99CCFF' WHERE id = 11;


            ";
            $this->executeDB($sql);

            $sql = "DELETE FROM pkg_category WHERE name = 'Inactivo' AND status = 0;";
            $this->executeDB($sql);

            $sql = "INSERT INTO `pkg_category` VALUES (99,'Inativo','',0,0);
            UPDATE `pkg_category` SET `id` = '0' WHERE `id` = 99;";
            $this->executeDB($sql);

            $sql = "ALTER TABLE  `pkg_category` ADD  `type` TINYINT( 1 ) NOT NULL DEFAULT  '1';";
            $this->executeDB($sql);

            $sql = "UPDATE  `pkg_category` SET  `type` =  '0' WHERE  `pkg_category`.`id` =0;";
            $this->executeDB($sql);

            $version = '3.0.5';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            $this->executeDB($sql);
        }

        if ($version == '3.0.5') {

            $sql = "ALTER TABLE `pkg_campaign` ADD `open_url` VARCHAR(200) NOT NULL DEFAULT '' AFTER `status`;
            ";
            $this->executeDB($sql);

            $version = '3.0.6';
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
