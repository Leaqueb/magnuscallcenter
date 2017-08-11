<?php
class UpdateMysqlCommand extends ConsoleCommand
{
    public $config;
    public $success;

    public function run($args)
    {

        $version = $this->config['global']['version'];

        echo $version;

        if ($version == '2.0.0') {

            $sql = "CREATE TABLE IF NOT EXISTS `pkg_codigos` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `prefix` int(20) NOT NULL,
					  `company` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
					  `favorito` int(1) NOT NULL DEFAULT 0,
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $sql = "ALTER TABLE  `pkg_phonebook` ADD  `portabilidadeFixed` TINYINT( 1 ) NOT NULL DEFAULT  '0',
				ADD  `portabilidadeMobile` TINYINT( 1 ) NOT NULL DEFAULT  '0'";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $sql = "UPDATE  pkg_configuration SET  status =  '1' WHERE  config_key ='portabilidadeUsername' OR config_key ='portabilidadePassword';";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }

            $sql = "INSERT INTO pkg_module VALUES (NULL, 't(''Portabilidade Codigos'')', 'portabilidadecodigos', 'trunk', '3')";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $idSubModule = Yii::app()->db->lastInsertID;

            $sql = "INSERT INTO pkg_group_module VALUES ((SELECT id FROM pkg_group_user WHERE id_user_type = 1 LIMIT 1), '" . $idSubModule . "', 'crud', '1', '1', '1');";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }

            $sql = "INSERT INTO pkg_module VALUES (10, 't(''DID'')', NULL, 'did', NULL)";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $idSubModule = Yii::app()->db->lastInsertID;

            $sql = "INSERT INTO pkg_group_module VALUES ((SELECT id FROM pkg_group_user WHERE id_user_type = 1 LIMIT 1), '" . $idSubModule . "', 'crud', '1', '1', '1');";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $sql = "INSERT INTO pkg_module VALUES (NULL, 't(''Destination'')', 'diddestination', 'diddestination', 10)";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $idSubModule = Yii::app()->db->lastInsertID;

            $sql = "INSERT INTO pkg_group_module VALUES ((SELECT id FROM pkg_group_user WHERE id_user_type = 1 LIMIT 1), '" . $idSubModule . "', 'crud', '1', '1', '1');";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $sql = "INSERT INTO pkg_module VALUES (NULL, 't(''Ivr'')', 'ivr', 'ivr', 10)";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $idSubModule = Yii::app()->db->lastInsertID;

            $sql = "INSERT INTO pkg_group_module VALUES ((SELECT id FROM pkg_group_user WHERE id_user_type = 1 LIMIT 1), '" . $idSubModule . "', 'crud', '1', '1', '1');";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }

            $sql = "CREATE TABLE IF NOT EXISTS `pkg_did_destination` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `id_ivr` int(11) DEFAULT NULL,
			  `id_user` int(11) DEFAULT NULL,
			  `id_campaign` int(11) DEFAULT NULL,
			  `did` varchar(16) NOT NULL,
			  `destination` varchar(120) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			  `creationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `activated` int(11) NOT NULL DEFAULT '1',
			  `voip_call` int(11) DEFAULT '0',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }

            $sql = "CREATE TABLE IF NOT EXISTS `pkg_ivr` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(50) NOT NULL,
			  `monFriStart` varchar(5) NOT NULL DEFAULT '09:00',
			  `monFriStop` varchar(5) NOT NULL DEFAULT '18:00',
			  `satStart` varchar(5) NOT NULL DEFAULT '09:00',
			  `satStop` varchar(5) NOT NULL DEFAULT '12:00',
			  `sunStart` varchar(5) NOT NULL DEFAULT '09:00',
			  `sunStop` varchar(5) NOT NULL DEFAULT '12:00',
			  `option_0` varchar(50) DEFAULT NULL,
			  `option_1` varchar(50) DEFAULT NULL,
			  `option_2` varchar(50) DEFAULT NULL,
			  `option_3` varchar(50) DEFAULT NULL,
			  `option_4` varchar(50) DEFAULT NULL,
			  `option_5` varchar(50) DEFAULT NULL,
			  `option_6` varchar(50) DEFAULT NULL,
			  `option_7` varchar(50) DEFAULT NULL,
			  `option_8` varchar(50) DEFAULT NULL,
			  `option_9` varchar(50) DEFAULT NULL,
			  `option_10` varchar(50) DEFAULT NULL,
			  `workaudio` varchar(100) DEFAULT NULL,
			  `noworkaudio` varchar(100) DEFAULT NULL,
			  `option_out_0` varchar(50) DEFAULT NULL,
			  `option_out_1` varchar(50) DEFAULT NULL,
			  `option_out_2` varchar(50) DEFAULT NULL,
			  `option_out_3` varchar(50) DEFAULT NULL,
			  `option_out_4` varchar(50) DEFAULT NULL,
			  `option_out_5` varchar(50) DEFAULT NULL,
			  `option_out_6` varchar(50) DEFAULT NULL,
			  `option_out_7` varchar(50) DEFAULT NULL,
			  `option_out_8` varchar(50) DEFAULT NULL,
			  `option_out_9` varchar(50) DEFAULT NULL,
			  `option_out_10` varchar(50) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }

            $sql = "CREATE TABLE pkg_codigos_trunks (
			  	`id_codigo` int(11) NOT NULL,
			  	`id_trunk` int(11) NOT NULL,
			  	PRIMARY KEY (`id_codigo`,`id_trunk`),
			  	KEY `fk_pkg_codigos_pkg_trunk` (`id_trunk`),
			  	CONSTRAINT `fk_pkg_codigos_pkg_trunk` FOREIGN KEY (`id_codigo`) REFERENCES `pkg_codigos` (`id`),
			  	CONSTRAINT `fk_pkg_trunk_pkg_codigos` FOREIGN KEY (`id_trunk`) REFERENCES `pkg_trunk` (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }

            $version = '2.0.1';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
        }

        if ($version == '2.0.1') {

            $sql = "ALTER TABLE  `pkg_pausas` ADD  `maximo` INT( 11 ) NOT NULL DEFAULT  '5'";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }

            $sql = "INSERT INTO pkg_module VALUES (NULL, 't(''Pausas'')', 'pausas', 'modules', 1)";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $idSubModule = Yii::app()->db->lastInsertID;

            $sql = "INSERT INTO pkg_group_module VALUES ((SELECT id FROM pkg_group_user WHERE id_user_type = 1 LIMIT 1), '" . $idSubModule . "', 'crud', '1', '1', '1');";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }

            $sql = "INSERT INTO `pkg_configuration` VALUES (NULL,'Callback add Prefix','callback_add_prefix','','Add prefix in callerd in callback call','global',1),
			(NULL,'Answer Callback','answer_callback','0','Answer callback and play audio','global',1),
			(NULL,'Callback remove Prefix','callback_remove_prefix','','Remove prefix in callerd in callback call','global',1),
			(NULL,'Diretorio dos audios','record_patch','/var/spool/asterisk/monitor','Diretorio onde estão os audios','global',1)";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }

            $version = '2.0.2';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
        }

        if ($version == '2.0.2') {
            $sql = "
			ALTER TABLE `pkg_campaign` DROP `out`;
			ALTER TABLE `pkg_campaign` DROP `predictive`;
			ALTER TABLE `pkg_campaign` DROP `secondusedreal`;
			ALTER TABLE `pkg_campaign` DROP `forward_number`;
			ALTER TABLE `pkg_campaign` DROP `audio`;
			ALTER TABLE `pkg_campaign` DROP `call_limit`;
			ALTER TABLE `pkg_campaign` DROP `call_next_try`";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
            $version = '2.0.3';
            $sql     = "UPDATE pkg_configuration SET config_value = '" . $version . "' WHERE config_key = 'version' ";
            try {
                Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
        }

    }
}
