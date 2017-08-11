-- phpMyAdmin SQL Dump
-- version 3.5.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 09, 2017 at 05:20 PM
-- Server version: 5.6.14
-- PHP Version: 5.6.28

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `callcenter`
--

-- --------------------------------------------------------

--
-- Table structure for table `pkg_asistencia`
--

CREATE TABLE IF NOT EXISTS `pkg_asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `day` date NOT NULL DEFAULT '0000-00-00',
  `turno` varchar(1) NOT NULL DEFAULT 'M',
  `start_time` time NOT NULL DEFAULT '08:00:00',
  `stop_time` time NOT NULL DEFAULT '13:00:00',
  `week_day` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pkg_user_pkg_asistencia` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_billing`
--

CREATE TABLE IF NOT EXISTS `pkg_billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user_online` int(11) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `total_price` varchar(15) DEFAULT '0',
  `turno` varchar(1) NOT NULL DEFAULT 'M',
  `efetivas` int(11) NOT NULL DEFAULT '0',
  `total_time` int(11) NOT NULL DEFAULT '0',
  `ratio` varchar(10) NOT NULL DEFAULT '0',
  `ratio_total` varchar(10) NOT NULL DEFAULT '0',
  `incremento` varchar(10) NOT NULL DEFAULT '0',
  `description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pkg_billing_pkg_user_online` (`id_user_online`),
  KEY `fk_pkg_billing_pkg_user` (`id_user`),
  KEY `fk_pkg_billing_pkg_campaign` (`id_campaign`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_call_online`
--

CREATE TABLE IF NOT EXISTS `pkg_call_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `canal` varchar(50) DEFAULT NULL,
  `tronco` varchar(50) DEFAULT NULL,
  `ndiscado` varchar(16) DEFAULT '0',
  `codec` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` varchar(16) NOT NULL,
  `duration` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `reinvite` varchar(5) NOT NULL,
  `time_start_cat` varchar(20) NOT NULL DEFAULT '0',
  `cant_cat` int(11) NOT NULL DEFAULT '0',
  `media_to_cat` int(11) NOT NULL DEFAULT '0',
  `categorizando` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_pkg_user_pkg_operador_online` (`id_user`),
  KEY `fk_pkg_campaign_pkg_operador_online` (`id_campaign`),
  KEY `starttime` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_campaign`
--

CREATE TABLE IF NOT EXISTS `pkg_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `creationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `startingdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expirationdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_bin,
  `status` int(11) NOT NULL DEFAULT '1',
  `daily_start_time` time NOT NULL DEFAULT '10:00:00',
  `daily_stop_time` time NOT NULL DEFAULT '18:00:00',
  `monday` tinyint(4) NOT NULL DEFAULT '1',
  `tuesday` tinyint(4) NOT NULL DEFAULT '1',
  `wednesday` tinyint(4) NOT NULL DEFAULT '1',
  `thursday` tinyint(4) NOT NULL DEFAULT '1',
  `friday` tinyint(4) NOT NULL DEFAULT '1',
  `saturday` tinyint(4) NOT NULL DEFAULT '0',
  `sunday` tinyint(4) NOT NULL DEFAULT '0',
  `allow_email` tinyint(1) NOT NULL DEFAULT '0',
  `allow_city` tinyint(1) NOT NULL DEFAULT '0',
  `allow_address` tinyint(1) NOT NULL DEFAULT '0',
  `allow_state` tinyint(1) NOT NULL DEFAULT '0',
  `allow_country` tinyint(1) NOT NULL DEFAULT '0',
  `allow_dni` tinyint(1) NOT NULL DEFAULT '0',
  `allow_mobile` tinyint(1) NOT NULL DEFAULT '0',
  `allow_number_home` tinyint(1) NOT NULL DEFAULT '0',
  `allow_number_office` tinyint(1) NOT NULL DEFAULT '0',
  `allow_zip_code` tinyint(1) NOT NULL DEFAULT '0',
  `allow_company` tinyint(1) NOT NULL DEFAULT '0',
  `allow_birth_date` tinyint(1) NOT NULL DEFAULT '0',
  `allow_type_user` tinyint(1) NOT NULL DEFAULT '0',
  `allow_sexo` tinyint(1) NOT NULL DEFAULT '0',
  `allow_edad` tinyint(1) NOT NULL DEFAULT '0',
  `allow_profesion` tinyint(1) NOT NULL DEFAULT '0',
  `allow_mobile_2` tinyint(1) NOT NULL DEFAULT '0',
  `allow_option_1` varchar(20) NOT NULL,
  `allow_option_2` varchar(20) NOT NULL,
  `allow_option_3` varchar(20) NOT NULL,
  `allow_option_4` varchar(20) NOT NULL,
  `allow_option_5` varchar(20) NOT NULL,
  `allow_number` tinyint(1) NOT NULL DEFAULT '1',
  `allow_name` tinyint(1) NOT NULL DEFAULT '1',
  `allow_id_phonebook` tinyint(1) NOT NULL DEFAULT '1',
  `allow_status` tinyint(1) NOT NULL DEFAULT '1',
  `allow_sessiontime` tinyint(1) NOT NULL DEFAULT '0',
  `daily_morning_start_time` time NOT NULL DEFAULT '08:00:00',
  `daily_morning_stop_time` time NOT NULL DEFAULT '13:00:00',
  `daily_afternoon_start_time` time NOT NULL DEFAULT '13:00:00',
  `daily_afternoon_stop_time` time NOT NULL DEFAULT '19:00:00',
  `allow_beneficio_number` int(11) NOT NULL DEFAULT '0',
  `allow_quantidade_transacoes` int(11) NOT NULL DEFAULT '0',
  `allow_inicio_beneficio` int(11) NOT NULL DEFAULT '0',
  `allow_beneficio_valor` int(11) NOT NULL DEFAULT '0',
  `allow_banco` int(11) NOT NULL DEFAULT '0',
  `allow_agencia` int(11) NOT NULL DEFAULT '0',
  `allow_conta` int(11) NOT NULL DEFAULT '0',
  `allow_endereco_complementar` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_fixo1` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_fixo2` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_fixo3` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_celular1` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_celular2` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_celular3` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_fixo_comercial1` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_fixo_comercial2` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_fixo_comercial3` int(11) NOT NULL DEFAULT '0',
  `allow_parente1` int(11) NOT NULL DEFAULT '0',
  `allow_fone_parente1` int(11) NOT NULL DEFAULT '0',
  `allow_parente2` int(11) NOT NULL DEFAULT '0',
  `allow_fone_parente2` int(11) NOT NULL DEFAULT '0',
  `allow_parente3` int(11) NOT NULL DEFAULT '0',
  `allow_fone_parente3` int(11) NOT NULL DEFAULT '0',
  `allow_vizinho1` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_vizinho1` int(11) NOT NULL DEFAULT '0',
  `allow_vizinho2` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_vizinho2` int(11) NOT NULL DEFAULT '0',
  `allow_vizinho3` int(11) NOT NULL DEFAULT '0',
  `allow_telefone_vizinho3` int(11) NOT NULL DEFAULT '0',
  `allow_email2` int(11) NOT NULL DEFAULT '0',
  `allow_email3` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=82 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_campaign_phonebook`
--

CREATE TABLE IF NOT EXISTS `pkg_campaign_phonebook` (
  `id_campaign` int(11) NOT NULL,
  `id_phonebook` int(11) NOT NULL,
  PRIMARY KEY (`id_campaign`,`id_phonebook`),
  KEY `fk_pkg_phonenumber_pkg_campaign_phonebook` (`id_phonebook`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_category`
--

CREATE TABLE IF NOT EXISTS `pkg_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_bin,
  `status` int(1) NOT NULL DEFAULT '1',
  `use_in_efetiva` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `pkg_category`
--

INSERT INTO `pkg_category` (`id`, `name`, `description`, `status`, `use_in_efetiva`) VALUES
(0, 'Inactivo', '', 1, 0),
(1, 'Activo', '', 1, 0),
(2, 'Volver a llamar', '', 1, 0),
(3, 'Contestada', '', 0, 0),
(4, 'No contesta', '', 1, 0),
(5, 'Numero invalido', '', 0, 0),
(6, 'Rechazo', '', 1, 0),
(7, 'Contestador', '', 1, 0),
(8, 'Base externa', '', 1, 0),
(10, 'No Pasa el Filtro', '', 1, 0),
(11, 'Efectiva', '', 1, 1),
(12, 'Inactivo', '', 1, 0),
(15, 'Contestador Automatico', 'Contestador Automatico', 0, 0),
(16, 'Hogar de sólo mujeres', 'Hogar de sólo mujeres', 0, 0),
(17, 'Error de Base', '', 0, 0),
(18, 'Ocupado', 'Ocupado', 1, 0),
(21, 'Numero Invalido', '', 0, 0),
(22, 'Inactivo', '', 0, 0),
(23, 'Entrevista Realizada', 'Entrevista contestada', 1, 0),
(24, 'No llamo al Centro de At. al C', 'No llamo al Centro de At. al C', 1, 0),
(25, 'Incorrecto', 'Incorrecto', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pkg_cdr`
--

CREATE TABLE IF NOT EXISTS `pkg_cdr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `id_phonebook` int(11) NOT NULL,
  `id_phonenumber` int(11) DEFAULT NULL,
  `id_trunk` int(11) DEFAULT NULL,
  `sessionid` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `uniqueid` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `starttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stoptime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sessiontime` int(11) DEFAULT NULL,
  `calledstation` varchar(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `id_category` int(11) DEFAULT NULL,
  `real_sessiontime` int(11) DEFAULT NULL,
  `dnid` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `terminatecauseid` int(1) DEFAULT '1',
  `pay` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `starttime` (`starttime`),
  KEY `calledstation` (`calledstation`),
  KEY `terminatecauseid` (`terminatecauseid`),
  KEY `uniqueid` (`uniqueid`),
  KEY `id_campaign` (`id_campaign`),
  KEY `id_trunk` (`id_trunk`),
  KEY `id_user` (`id_user`),
  KEY `id_phonebook` (`id_phonebook`),
  KEY `id_category` (`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_codigos`
--

CREATE TABLE IF NOT EXISTS `pkg_codigos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` int(20) NOT NULL,
  `company` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `favorito` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=177 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_codigos_trunks`
--

CREATE TABLE IF NOT EXISTS `pkg_codigos_trunks` (
  `id_codigo` int(11) NOT NULL,
  `id_trunk` int(11) NOT NULL,
  PRIMARY KEY (`id_codigo`,`id_trunk`),
  KEY `fk_pkg_codigos_pkg_trunk` (`id_trunk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_configuration`
--

CREATE TABLE IF NOT EXISTS `pkg_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_title` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `config_key` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `config_value` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `config_description` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `config_group_title` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `status` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `pkg_configuration`
--

INSERT INTO `pkg_configuration` (`id`, `config_title`, `config_key`, `config_value`, `config_description`, `config_group_title`, `status`) VALUES
(1, 'Language', 'base_language', 'pt_BR', 'Allow \n en English \nes Espanhol \npt_BR Portugues', 'global', 1),
(2, 'Version', 'version', '2.1.0', 'MagnusCallcenter Version', 'global', 1),
(3, 'Licence', 'licence', 'free', 'MagnusCallcenter Licence', 'global', 0),
(4, 'Server IP', 'ip_servers', '', 'Ip do servidor MagnusCallcenter', 'global', 1),
(5, 'Template', 'template', 'green-neptune', 'Allowed values:\ngreen, gray, blue, yellow, red, orange, purple', 'global', 0),
(6, 'Country', 'base_country', 'BRL', 'Allowed values\nUSA United States,\nBRL Brasil,\nARG Argentina,\nNLD Netherlands,\nESP Spanish', 'global', 1),
(7, 'Desktop layout', 'layout', '0', 'Active Desktop template, only to FULL version\n1 - Enable (Only to full version)\n0 - Disable', 'global', 0),
(8, 'Wallpaper', 'wallpaper', 'Azul', 'Default Wallpaper, only FULL version.', 'global', 0),
(9, 'Admin Email', 'admin_email', 'info@magnusbilling.com', 'Email for receive notifications', 'global', 1),
(10, 'Send email copy to admin', 'admin_received_email', '1', 'Send copy for admin email', 'global', 1),
(11, 'Archive cdr', 'archive_call_prior_x_month', '4', 'Calls to file before 10 months.', 'global', 1),
(12, 'Decimal precision', 'decimal_precision', '0000', 'Decimal precision.', 'global', 1),
(13, 'Portabilidade Usuário', 'portabilidadeUsername', '0', 'Usuário da portabilidade para consulta via WebService', 'global', 1),
(14, 'Portabilidade Senha', 'portabilidadePassword', '0', 'Senha da portabilidade para consulta via WebService', 'global', 1),
(15, 'Tempo para gerar chamada para o operador após a 1º tentativa', 'operator_next_try', '30', 'Tempo para gerar chamada para o operador após a 1º tentativa', 'global', 1),
(16, 'Botao atualizar em LOTE', 'updateAll', '0', 'Ativar o botao Atualizar em Lote nos menus', 'global', 1),
(17, 'Limite de chamadas', 'campaign_limit', '2', 'Limite de chamadas realizadas por cada operador disponível', 'global', 1),
(18, 'Desconto por atraso', 'tardanza', '15', 'Valor a descontar do operador por atraso', 'global', 1),
(19, 'Preco Onibus', 'valor_colectivo', '10.2', 'Valor do Onibus', 'global', 1),
(20, 'Preco por hora, ratio < 0,5', 'valor_hora_zero', '11', 'Preco por hora em campanhas con ratio menor que zero', 'global', 1),
(21, 'Preco por hora', 'valor_hora', '16', 'Preco por hora', 'global', 1),
(22, 'Preco por falta', 'valor_falta', '50', 'Preco por falta', 'global', 1),
(23, 'Url para notificar apos salvar numero', 'notify_url_after_save_number', NULL, 'Url para notificar apos salvar numero', 'global', 0),
(24, 'Categorias para notificar', 'notify_url_category', NULL, 'Categorias para notificar', 'global', 0),
(25, 'AGI 1 - Answer Call', 'answer_call', '0', 'If enabled the MagnusCallcenter answers the call that starts.\nDefault: 0', 'agi-conf1', 1),
(26, 'AGI 1 - User DNID', 'use_dnid', '1', 'If the client does not need active schedule again the number he wish to call after entering the PIN.\n\n1 - Enable (DEFAULT)\n0 - Disable', 'agi-conf1', 1),
(27, 'AGI 1 - Recording calls', 'record_call', '0', 'Enables recording of all customers.\nCAUTION, THIS OPTION REQUIRES A LOT OF SERVER PERFORMANCE. SO YOU CAN RECORD CUSTOMER SPECIFIC.\n\n0: Disable\n1: Enable', 'agi-conf1', 1),
(28, 'AGI 1 - International prefixes', 'international_prefixes', '00,09', 'List the prefixes you want stripped off if the call number', 'agi-conf1', 1),
(29, 'AGI 1 - FailOver LCR/LCD', 'failover_lc_prefix', '1', 'If anable and have two hidden tariff in de plan, MagnusCallcenter gonna get the cheaper', 'agi-conf1', 1),
(30, 'AGI 1 - Dial Command Params', 'dialcommand_param', ',60,L(%timeout%:61000:30000)', 'More info: http://voip-info.org/wiki-Asterisk+cmd+dial', 'agi-conf1', 1),
(31, 'AGI 1 - Internal Call, Dial Command Params', 'dialcommand_param_sipiax_friend', ',60,TtiL(3600000:61000:30000)', 'Dial paramater for call between users.\n\nby default (3600000  =  1HOUR MAX CALL).', 'agi-conf1', 1),
(32, 'AGI 1 - Failover Retry Limit', 'failover_recursive_limit', '5', 'Define how many time we want to authorize the research of the failover trunk when a call fails', 'agi-conf1', 1),
(33, 'AGI 1 - Outbound Call', 'switchdialcommand', '0', 'Define the order to make the outbound call<br>YES -> SIP/number@trunk - NO  SIP/trunk/number<br>Both should work exactly the same but i experimented one case when gateway was supporting number@trunk, So in case of trouble, try it out.', 'agi-conf1', 1),
(34, 'AGI 1 - Say Balance After Call', 'say_balance_after_call', '0', 'Play the balance to the user after the call\n\n0 - No\n1 - Yes', 'agi-conf1', 1),
(35, 'SIP Account for spy call', 'channel_spy', '0', 'SIP Account for spy call', 'global', 1),
(36, 'Menu color', 'color_menu', 'White', 'Menu color, Black or White', 'global', 0),
(37, 'Charge Sip Call', 'charge_sip_call', '0', 'Charge sip call between clients', 'global', 1),
(38, 'URL to extra module', 'module_extra', 'index.php/extra/read', 'Url to extra module, default: index.php/extra/read', 'global', 1),
(39, 'intra/inter Billing', 'intra-inter', '0', 'Enable Intra-Inter Billing. If you enable this option, and you have another plan with the same name + Intra on the name Mbilling use the new plan to intra call', 'global', 1),
(40, 'Asterisk', 'asterisk_version', '11', 'Set your Asterisk Version instaled. Default 1.8', 'global', 1),
(41, 'Tts URL', 'tts_url', 'https://translate.google.com/translate_tts?ie=UTF-8&q=$name&tl=pt-BR&total=1&idx=0&textlen=25&client=t&tk=55786|34299.', 'Set here the URL to use in Massive Call. Use variable $name in the string field', 'global', 1),
(42, 'MixMonitor Format', 'MixMonitor_format', 'gsm', 'see the availables extensions in http://www.voip-info.org/wiki/view/MixMonitor', 'global', 1),
(43, 'AGI 1 - Use amd macro', 'amd', '0', 'Use amd. Set to CM(amd) . \n       Add this macro in your extension_magnus.conf\n\n        [macro-amd]\n       exten => s,1,AMD\n        exten => s,n,Noop(AMD_NUMERO - ${CALLERID(num)})\n        exten => s,n,Noop(AMD_STATUS - ${AMDSTATUS})\n        exten => s,n,Noop(AMD_CAUSE - ${AMDCAUSE})\n        exten => s,n,GotoIf($[${AMDSTATUS}=HUMAN]?humn:mach)\n        exten => s,n(mach),SoftHangup(${CHANNEL})\n       exten => s,n,Hangup()\n       exten => s,n(humn),WaitForSilence(20)', 'agi-conf1', 1),
(44, 'Callback add Prefix', 'callback_add_prefix', '', 'Add prefix in callerd in callback call', 'global', 1),
(45, 'Callback remove Prefix', 'callback_remove_prefix', '', 'Remove prefix in callerd in callback call', 'global', 1),
(46, 'Answer Callback', 'answer_callback', '0', 'Answer callback and play audio', 'global', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pkg_did_destination`
--

CREATE TABLE IF NOT EXISTS `pkg_did_destination` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_group_module`
--

CREATE TABLE IF NOT EXISTS `pkg_group_module` (
  `id_group` int(11) NOT NULL,
  `id_module` int(11) NOT NULL,
  `action` varchar(45) DEFAULT NULL,
  `show_menu` tinyint(1) NOT NULL,
  `createShortCut` tinyint(1) NOT NULL DEFAULT '0',
  `createQuickStart` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_group`,`id_module`),
  KEY `fk_pkg_module_pkg_group_module` (`id_module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pkg_group_module`
--

INSERT INTO `pkg_group_module` (`id_group`, `id_module`, `action`, `show_menu`, `createShortCut`, `createQuickStart`) VALUES
(1, 1, 'crud', 1, 0, 0),
(1, 2, 'rcud', 1, 0, 0),
(1, 3, 'crud', 1, 0, 0),
(1, 4, 'crud', 1, 0, 0),
(1, 5, 'crud', 1, 0, 0),
(1, 6, 'r', 0, 0, 0),
(1, 7, 'crud', 1, 1, 1),
(1, 8, 'crud', 1, 0, 0),
(1, 9, 'crud', 0, 0, 0),
(1, 10, 'crud', 1, 0, 0),
(1, 65, 'cru', 1, 0, 0),
(1, 66, 'crud', 1, 1, 0),
(1, 67, 'crud', 1, 0, 0),
(1, 69, 'crud', 1, 1, 0),
(1, 71, 'crud', 1, 1, 0),
(1, 72, 'crud', 1, 1, 0),
(1, 74, 'crud', 1, 0, 0),
(1, 75, 'crud', 1, 0, 0),
(1, 76, 'crud', 1, 1, 0),
(1, 77, 'rd', 1, 1, 0),
(1, 79, 'crud', 1, 0, 0),
(1, 80, 'rd', 1, 1, 1),
(1, 81, 'crud', 1, 0, 0),
(1, 82, 'cru', 1, 0, 0),
(1, 83, 'ru', 1, 0, 0),
(1, 85, 'r', 1, 1, 1),
(1, 86, 'r', 1, 0, 0),
(1, 87, 'crud', 1, 0, 0),
(1, 88, 'crud', 1, 0, 0),
(1, 90, 'crud', 1, 0, 0),
(1, 91, 'crud', 1, 0, 0),
(2, 4, 'r', 1, 1, 0),
(2, 69, 'r', 0, 1, 0),
(2, 71, 'r', 0, 1, 0),
(2, 72, 'ruc', 1, 1, 1),
(2, 79, 'r', 0, 1, 0),
(2, 81, 'ru', 1, 1, 0),
(3, 5, 'r', 1, 1, 1),
(3, 7, 'r', 0, 0, 0),
(3, 74, 'r', 1, 1, 1),
(3, 81, 'ru', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pkg_group_user`
--

CREATE TABLE IF NOT EXISTS `pkg_group_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `id_user_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pkg_user_type_pkg_group_user` (`id_user_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `pkg_group_user`
--

INSERT INTO `pkg_group_user` (`id`, `name`, `id_user_type`) VALUES
(1, 'Administrator', 1),
(2, 'Operador', 2),
(3, 'Customers', 2),
(4, 'Gerentes', 1),
(5, 'Especial', 2),
(6, 'Clientes', 3);

-- --------------------------------------------------------

--
-- Table structure for table `pkg_ivr`
--

CREATE TABLE IF NOT EXISTS `pkg_ivr` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_log`
--

CREATE TABLE IF NOT EXISTS `pkg_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_bin,
  `action` varchar(20) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_logins`
--

CREATE TABLE IF NOT EXISTS `pkg_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `starttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stoptime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_time` int(11) DEFAULT NULL,
  `turno` varchar(1) NOT NULL DEFAULT 'M',
  `type` varchar(10) DEFAULT NULL,
  `pause_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pkg_user_pkg_user_online` (`id_user`),
  KEY `fk_pkg_campaign_pkg_user_online` (`id_campaign`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_module`
--

CREATE TABLE IF NOT EXISTS `pkg_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` varchar(100) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `icon_cls` varchar(100) DEFAULT NULL,
  `id_module` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pkg_module_module` (`id_module`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=92 ;

--
-- Dumping data for table `pkg_module`
--

INSERT INTO `pkg_module` (`id`, `text`, `module`, `icon_cls`, `id_module`) VALUES
(1, 't(''Users'')', NULL, 'users', NULL),
(2, 't(''Administration'')', NULL, 'icon-settings', NULL),
(3, 't(''Trocales'')', NULL, 'routes', NULL),
(4, 't(''Canpañas'')', NULL, 'campaignpollinfo', NULL),
(5, 't(''Informes'')', NULL, 'report', NULL),
(6, 't(''Module'')', 'module', 'module', 2),
(7, 't(''Operadores'')', 'user', 'callerid', 1),
(8, 't(''GroupUser'')', 'groupuser', 'modules', 2),
(9, 't(''GroupModule'')', 'groupmodule', 'groupmodule', 2),
(10, 't(''DID'')', NULL, 'did', NULL),
(65, 't(''Config'')', 'configuration', 'config', 2),
(66, 't(''Trunk'')', 'trunk', 'trunk', 3),
(67, 't(''Provider'')', 'provider', 'provider', 3),
(69, 't(''Campañas'')', 'campaign', 'offeruse', 4),
(71, 't(''Agendas'')', 'phonebook', 'offercdr', 4),
(72, 't(''Numeros'')', 'phonenumber', 'numbers', 4),
(73, 't(''Resumen por Usuario'')', 'cdrsummarybyuser', 'tariffs', 5),
(74, 't(''Cdr'')', 'cdr', 'cdr', 5),
(75, 't(''Resumen CDR'')', 'cdrsummary', 'callsummary', 5),
(76, 't(''CallOnLine'')', 'callonline', 'callonline', 5),
(77, 't(''Usuarios OnLine'')', 'useronline', 'offer', 5),
(79, 't(''Categorias'')', 'category', 'queues', 4),
(80, 't(''CdrSumaryOperador'')', 'cdrsumaryoperador', 'callsummary', 5),
(81, 't(''Asistencia'')', 'asistencia', 'refill', 4),
(82, 't(''Billing'')', NULL, 'paymentmethods', NULL),
(83, 't(''Payments'')', 'billing', 'refillprovider', 82),
(84, 't(''Pools'')', 'pools', 'cdr', 1),
(85, 't(''Dashboard'')', 'dashboard', 'callback', 2),
(86, 't(''Predictive Report'')', 'campaignpredictive', 'callback', 5),
(87, 't(''Pausas'')', 'pausas', 'modules', 1),
(88, 't(''Portabilidade Codigos'')', 'portabilidadecodigos', 'trunk', 3),
(90, 't(''Destination'')', 'diddestination', 'diddestination', 10),
(91, 't(''Ura'')', 'ivr', 'ivr', 10);

-- --------------------------------------------------------

--
-- Table structure for table `pkg_pausas`
--

CREATE TABLE IF NOT EXISTS `pkg_pausas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` smallint(1) NOT NULL DEFAULT '1',
  `start_time` time NOT NULL DEFAULT '08:00:00',
  `stop_time` time NOT NULL DEFAULT '13:00:00',
  `obrigatoria` tinyint(1) NOT NULL DEFAULT '0',
  `maximo` int(11) NOT NULL DEFAULT '5',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_phonebook`
--

CREATE TABLE IF NOT EXISTS `pkg_phonebook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_trunk` int(11) NOT NULL,
  `id_campaign` int(11) DEFAULT NULL,
  `name` char(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `show_numbers_operator` int(11) NOT NULL DEFAULT '1',
  `reprocessar` int(2) NOT NULL DEFAULT '0',
  `find_location` int(1) NOT NULL DEFAULT '0',
  `portabilidadeFixed` tinyint(1) NOT NULL DEFAULT '0',
  `portabilidadeMobile` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_pkg_trunk_pkg_phonebook` (`id_trunk`),
  KEY `fk_pkg_campaign_pkg_phonebook` (`id_campaign`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_phonenumber`
--

CREATE TABLE IF NOT EXISTS `pkg_phonenumber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_phonebook` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `number` bigint(30) NOT NULL,
  `status` int(11) DEFAULT '1',
  `name` char(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(60) NOT NULL DEFAULT '',
  `creationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_category` int(11) DEFAULT NULL,
  `datebackcall` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cita_concreta` int(1) NOT NULL DEFAULT '0',
  `info` mediumtext CHARACTER SET utf8 COLLATE utf8_bin,
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `address` varchar(150) NOT NULL,
  `state` varchar(40) NOT NULL DEFAULT '',
  `country` varchar(40) NOT NULL DEFAULT '',
  `dni` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `number_home` varchar(20) DEFAULT NULL,
  `number_office` varchar(20) DEFAULT NULL,
  `zip_code` varchar(20) NOT NULL DEFAULT '',
  `company` varchar(50) NOT NULL DEFAULT '',
  `birth_date` varchar(20) DEFAULT NULL,
  `type_user` varchar(50) NOT NULL DEFAULT '',
  `sexo` varchar(10) NOT NULL DEFAULT '',
  `edad` varchar(11) DEFAULT NULL,
  `profesion` varchar(50) NOT NULL DEFAULT '',
  `mobile_2` varchar(20) DEFAULT NULL,
  `option_1` varchar(80) NOT NULL DEFAULT '',
  `option_2` varchar(80) NOT NULL DEFAULT '',
  `option_3` varchar(80) NOT NULL DEFAULT '',
  `option_4` varchar(80) NOT NULL DEFAULT '',
  `option_5` varchar(80) NOT NULL DEFAULT '',
  `sessiontime` int(11) NOT NULL DEFAULT '0',
  `gps` varchar(30) NOT NULL DEFAULT '',
  `beneficio_number` varchar(50) DEFAULT NULL,
  `quantidade_transacoes` varchar(50) DEFAULT NULL,
  `inicio_beneficio` varchar(50) DEFAULT NULL,
  `beneficio_valor` varchar(50) DEFAULT NULL,
  `banco` varchar(50) DEFAULT NULL,
  `agencia` varchar(50) DEFAULT NULL,
  `conta` varchar(50) DEFAULT NULL,
  `endereco_complementar` varchar(50) DEFAULT NULL,
  `telefone_fixo1` varchar(50) DEFAULT NULL,
  `telefone_fixo2` varchar(50) DEFAULT NULL,
  `telefone_fixo3` varchar(50) DEFAULT NULL,
  `telefone_celular1` varchar(50) DEFAULT NULL,
  `telefone_celular2` varchar(50) DEFAULT NULL,
  `telefone_celular3` varchar(50) DEFAULT NULL,
  `telefone_fixo_comercial1` varchar(50) DEFAULT NULL,
  `telefone_fixo_comercial2` varchar(60) DEFAULT NULL,
  `telefone_fixo_comercial3` varchar(60) DEFAULT NULL,
  `parente1` varchar(100) DEFAULT NULL,
  `fone_parente1` varchar(60) DEFAULT NULL,
  `parente2` varchar(60) DEFAULT NULL,
  `fone_parente2` varchar(60) DEFAULT NULL,
  `parente3` varchar(60) DEFAULT NULL,
  `fone_parente3` varchar(60) DEFAULT NULL,
  `vizinho1` varchar(100) DEFAULT NULL,
  `telefone_vizinho1` varchar(60) DEFAULT NULL,
  `vizinho2` varchar(100) DEFAULT NULL,
  `telefone_vizinho2` varchar(60) DEFAULT NULL,
  `vizinho3` varchar(100) DEFAULT NULL,
  `telefone_vizinho3` varchar(60) DEFAULT NULL,
  `email2` varchar(100) DEFAULT NULL,
  `email3` varchar(100) DEFAULT NULL,
  `timeCall` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_pkg_phonebook_pkg_phonenumber` (`id_phonebook`),
  KEY `number` (`number`),
  KEY `id_user` (`id_user`),
  KEY `fk_pkg_category_pkg_phonenumber` (`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_pools`
--

CREATE TABLE IF NOT EXISTS `pkg_pools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL,
  `type` varchar(20) NOT NULL,
  `answer_0` varchar(200) NOT NULL,
  `id_polls_0` int(11) DEFAULT NULL,
  `answer_1` varchar(200) DEFAULT NULL,
  `id_polls_1` int(11) DEFAULT NULL,
  `answer_2` varchar(200) DEFAULT NULL,
  `id_polls_2` int(11) DEFAULT NULL,
  `answer_3` varchar(200) DEFAULT NULL,
  `id_polls_3` int(11) DEFAULT NULL,
  `answer_4` varchar(200) DEFAULT NULL,
  `id_polls_4` int(11) DEFAULT NULL,
  `answer_5` varchar(200) DEFAULT NULL,
  `id_polls_5` int(11) DEFAULT NULL,
  `answer_6` varchar(200) DEFAULT NULL,
  `id_polls_6` int(11) DEFAULT NULL,
  `answer_7` varchar(200) DEFAULT NULL,
  `id_polls_7` int(11) DEFAULT NULL,
  `answer_8` varchar(200) DEFAULT NULL,
  `id_polls_8` int(11) DEFAULT NULL,
  `answer_9` varchar(200) DEFAULT NULL,
  `id_polls_9` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_predictive`
--

CREATE TABLE IF NOT EXISTS `pkg_predictive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniqueid` varchar(50) NOT NULL,
  `number` varchar(20) NOT NULL,
  `operador` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniqueid` (`uniqueid`),
  KEY `number` (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_preditive_gen`
--

CREATE TABLE IF NOT EXISTS `pkg_preditive_gen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) DEFAULT NULL,
  `uniqueID` varchar(30) DEFAULT NULL,
  `id_phonebook` int(11) DEFAULT NULL,
  `ringing_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniqueID` (`uniqueID`),
  KEY `ringing_time` (`ringing_time`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_preditive_refresh_number`
--

CREATE TABLE IF NOT EXISTS `pkg_preditive_refresh_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operador` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_provider`
--

CREATE TABLE IF NOT EXISTS `pkg_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_name` char(30) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `creationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` mediumtext CHARACTER SET utf8 COLLATE utf8_bin,
  `credit` decimal(10,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cons_pkg_provider_provider_name` (`provider_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_queue`
--

CREATE TABLE IF NOT EXISTS `pkg_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `musiconhold` varchar(128) DEFAULT NULL,
  `announce` varchar(128) DEFAULT NULL,
  `context` varchar(128) DEFAULT NULL,
  `timeout` int(11) DEFAULT NULL,
  `monitor_join` tinyint(1) DEFAULT NULL,
  `monitor_format` varchar(128) DEFAULT NULL,
  `queue_youarenext` varchar(128) DEFAULT NULL,
  `queue_thereare` varchar(128) DEFAULT NULL,
  `queue_callswaiting` varchar(128) DEFAULT NULL,
  `queue_holdtime` varchar(128) DEFAULT NULL,
  `queue_minutes` varchar(128) DEFAULT NULL,
  `queue_seconds` varchar(128) DEFAULT NULL,
  `queue_lessthan` varchar(128) DEFAULT NULL,
  `queue_thankyou` varchar(128) DEFAULT NULL,
  `queue_reporthold` varchar(128) DEFAULT NULL,
  `announce_frequency` int(11) DEFAULT NULL,
  `announce_round_seconds` int(11) DEFAULT NULL,
  `announce_holdtime` varchar(128) DEFAULT NULL,
  `retry` int(11) DEFAULT NULL,
  `wrapuptime` int(11) DEFAULT NULL,
  `maxlen` int(11) DEFAULT NULL,
  `servicelevel` int(11) DEFAULT NULL,
  `strategy` varchar(128) DEFAULT NULL,
  `joinempty` varchar(128) DEFAULT NULL,
  `leavewhenempty` varchar(128) DEFAULT NULL,
  `eventmemberstatus` tinyint(1) DEFAULT NULL,
  `eventwhencalled` tinyint(1) DEFAULT NULL,
  `reportholdtime` tinyint(1) DEFAULT NULL,
  `memberdelay` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `timeoutrestart` tinyint(1) DEFAULT NULL,
  `periodic_announce` varchar(50) DEFAULT NULL,
  `periodic_announce_frequency` int(11) DEFAULT NULL,
  `ringinuse` tinyint(1) DEFAULT NULL,
  `setinterfacevar` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_pkg_user_pkg_queue` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_queue_member`
--

CREATE TABLE IF NOT EXISTS `pkg_queue_member` (
  `uniqueid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `membername` varchar(40) DEFAULT NULL,
  `queue_name` varchar(128) DEFAULT NULL,
  `interface` varchar(128) DEFAULT NULL,
  `penalty` int(11) DEFAULT NULL,
  `paused` int(11) DEFAULT NULL,
  PRIMARY KEY (`uniqueid`),
  UNIQUE KEY `queue_interface` (`queue_name`,`interface`),
  KEY `fk_pkg_user_pkg_queue_member` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_sip`
--

CREATE TABLE IF NOT EXISTS `pkg_sip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `name` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `accountcode` varchar(11) NOT NULL,
  `regexten` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `amaflags` char(7) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `callgroup` char(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `callerid` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `canreinvite` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'no',
  `context` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `DEFAULTip` char(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `dtmfmode` char(7) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'RFC2833',
  `fromuser` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `fromdomain` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `host` varchar(31) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `insecure` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `language` char(2) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mailbox` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `md5secret` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `nat` char(3) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'yes',
  `deny` varchar(95) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `permit` varchar(95) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mask` varchar(95) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pickupgroup` char(10) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `port` char(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `qualify` char(7) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'yes',
  `restrictcid` char(1) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `rtptimeout` char(3) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `rtpholdtimeout` char(3) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `secret` varchar(20) DEFAULT NULL,
  `type` char(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'friend',
  `username` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `disallow` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'all',
  `allow` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `musiconhold` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `regseconds` int(11) NOT NULL DEFAULT '0',
  `ipaddr` char(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `cancallforward` char(3) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'yes',
  `fullcontact` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `setvar` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `regserver` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `lastms` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `directrtpsetup` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'no',
  `defaultuser` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `auth` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `subscribemwi` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `vmexten` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `cid_number` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `callingpres` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `usereqphone` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `incominglimit` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `subscribecontext` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `musicclass` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `mohsuggest` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `allowtransfer` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'no',
  `autoframing` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `maxcallbitrate` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `outboundproxy` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `rtpkeepalive` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0',
  `compactheaders` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `relaxdtmf` varchar(4) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `useragent` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `calllimit` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `host` (`host`),
  KEY `ipaddr` (`ipaddr`),
  KEY `port` (`port`),
  KEY `sip_friend_hp_index` (`host`,`port`),
  KEY `sip_friend_ip_index` (`ipaddr`,`port`),
  KEY `fk_pkg_user_pkg_sip` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_trunk`
--

CREATE TABLE IF NOT EXISTS `pkg_trunk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_provider` int(11) NOT NULL,
  `failover_trunk` int(11) DEFAULT NULL,
  `trunkcode` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `host` varchar(100) NOT NULL,
  `trunkprefix` char(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `providertech` char(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `providerip` char(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `removeprefix` char(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `secondusedreal` int(11) DEFAULT '0',
  `secondusedcarrier` int(11) DEFAULT '0',
  `secondusedratecard` int(11) DEFAULT '0',
  `creationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `addparameter` char(120) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `inuse` int(11) DEFAULT '0',
  `maxuse` int(11) DEFAULT '-1',
  `status` int(11) DEFAULT '1',
  `if_max_use` int(11) DEFAULT '0',
  `user` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `secret` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `allow` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `link_sms` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `canreinvite` char(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'no',
  `context` char(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'billing',
  `dtmfmode` char(7) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'RFC2833',
  `insecure` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'port,invite',
  `nat` char(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'yes',
  `qualify` char(7) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'yes',
  `type` char(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'peer',
  `disallow` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'all',
  `sms_res` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'all',
  PRIMARY KEY (`id`),
  KEY `fk_pkg_provider_pkg_trunk` (`id_provider`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_user`
--

CREATE TABLE IF NOT EXISTS `pkg_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_group` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` char(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `direction` char(80) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `zipcode` char(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `state` char(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `phone` char(30) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `mobile` char(30) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `datecreation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(70) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `country` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `company` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` text,
  `campaign_login` int(11) DEFAULT NULL,
  `usuario_tns` varchar(50) DEFAULT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `training` tinyint(1) NOT NULL DEFAULT '0',
  `conta` varchar(15) NOT NULL DEFAULT '',
  `agencia` varchar(15) NOT NULL DEFAULT '',
  `banck` varchar(25) NOT NULL DEFAULT '',
  `salary` varchar(15) NOT NULL DEFAULT '',
  `cargo` varchar(50) NOT NULL DEFAULT '',
  `stoptcontract` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `startcontract` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `worktime` varchar(25) NOT NULL DEFAULT '',
  `estadocivil` varchar(15) NOT NULL DEFAULT '',
  `escolaridade` varchar(25) NOT NULL DEFAULT '',
  `fathername` varchar(50) NOT NULL DEFAULT '',
  `dni` varchar(25) NOT NULL DEFAULT '',
  `cpf` varchar(25) NOT NULL DEFAULT '',
  `birthday` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hometown` varchar(50) NOT NULL DEFAULT '',
  `mothername` varchar(25) NOT NULL DEFAULT '',
  `webphone` tinyint(1) NOT NULL DEFAULT '0',
  `pause_obrigatorio` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `fk_pkg_group_user_pkg_user` (`id_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `pkg_user`
--

INSERT INTO `pkg_user` (`id`, `id_group`, `username`, `password`, `name`, `direction`, `zipcode`, `state`, `phone`, `mobile`, `status`, `datecreation`, `email`, `country`, `city`, `company`, `description`, `campaign_login`, `usuario_tns`, `last_login`, `training`, `conta`, `agencia`, `banck`, `salary`, `cargo`, `stoptcontract`, `startcontract`, `worktime`, `estadocivil`, `escolaridade`, `fathername`, `dni`, `cpf`, `birthday`, `hometown`, `mothername`, `webphone`, `pause_obrigatorio`) VALUES
(1, 1, 'admin', 'magnus', 'Magnus', NULL, NULL, NULL, NULL, NULL, 1, '2014-02-12 19:41:42', NULL, NULL, NULL, '', NULL, NULL, NULL, '2017-03-09 18:26:45', 0, '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', '', '', '0000-00-00 00:00:00', 'torres', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pkg_user_asistencia`
--

CREATE TABLE IF NOT EXISTS `pkg_user_asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_asistencia` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pkg_asistencia_pkg_user_asistencia` (`id_asistencia`),
  KEY `fk_pkg_user_pkg_user_asistencia` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_user_campaign`
--

CREATE TABLE IF NOT EXISTS `pkg_user_campaign` (
  `id_user` int(11) NOT NULL,
  `id_campaign` int(11) NOT NULL,
  PRIMARY KEY (`id_user`,`id_campaign`),
  KEY `fk_pkg_campaign_pkg_user_campaign` (`id_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_user_online`
--

CREATE TABLE IF NOT EXISTS `pkg_user_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_campaign` int(11) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `starttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stoptime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_time` int(11) DEFAULT NULL,
  `pause` int(11) NOT NULL,
  `pausetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_pause` int(11) NOT NULL DEFAULT '0',
  `turno` varchar(1) NOT NULL DEFAULT 'M',
  PRIMARY KEY (`id`),
  KEY `fk_pkg_user_pkg_user_online` (`id_user`),
  KEY `fk_pkg_campaign_pkg_user_online` (`id_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pkg_user_type`
--

CREATE TABLE IF NOT EXISTS `pkg_user_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `pkg_user_type`
--

INSERT INTO `pkg_user_type` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Operador'),
(3, 'Cliente');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pkg_asistencia`
--
ALTER TABLE `pkg_asistencia`
  ADD CONSTRAINT `fk_pkg_operador_asistencia` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);

--
-- Constraints for table `pkg_billing`
--
ALTER TABLE `pkg_billing`
  ADD CONSTRAINT `fk_pkg_billing_pkg_campaign` FOREIGN KEY (`id_campaign`) REFERENCES `pkg_campaign` (`id`),
  ADD CONSTRAINT `fk_pkg_billing_pkg_user` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`),
  ADD CONSTRAINT `fk_pkg_billing_pkg_user_online` FOREIGN KEY (`id_user_online`) REFERENCES `pkg_user_online` (`id`);

--
-- Constraints for table `pkg_campaign_phonebook`
--
ALTER TABLE `pkg_campaign_phonebook`
  ADD CONSTRAINT `fk_pkg_campaign_pkg_campaign_phonebook` FOREIGN KEY (`id_campaign`) REFERENCES `pkg_campaign` (`id`),
  ADD CONSTRAINT `fk_pkg_phonebook_pkg_campaign_phonebook` FOREIGN KEY (`id_phonebook`) REFERENCES `pkg_phonebook` (`id`);

--
-- Constraints for table `pkg_cdr`
--
ALTER TABLE `pkg_cdr`
  ADD CONSTRAINT `fk_pkg_campaign_pkg_cdr` FOREIGN KEY (`id_campaign`) REFERENCES `pkg_campaign` (`id`),
  ADD CONSTRAINT `fk_pkg_category_pkg_cdr` FOREIGN KEY (`id_category`) REFERENCES `pkg_category` (`id`),
  ADD CONSTRAINT `fk_pkg_phonebook_pkg_cdr` FOREIGN KEY (`id_phonebook`) REFERENCES `pkg_phonebook` (`id`),
  ADD CONSTRAINT `fk_pkg_trunk_pkg_cdr` FOREIGN KEY (`id_trunk`) REFERENCES `pkg_trunk` (`id`),
  ADD CONSTRAINT `fk_pkg_user_pkg_cdr` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);

--
-- Constraints for table `pkg_codigos_trunks`
--
ALTER TABLE `pkg_codigos_trunks`
  ADD CONSTRAINT `fk_pkg_codigos_pkg_trunk` FOREIGN KEY (`id_codigo`) REFERENCES `pkg_codigos` (`id`),
  ADD CONSTRAINT `fk_pkg_trunk_pkg_codigos` FOREIGN KEY (`id_trunk`) REFERENCES `pkg_trunk` (`id`);

--
-- Constraints for table `pkg_group_module`
--
ALTER TABLE `pkg_group_module`
  ADD CONSTRAINT `fk_pkg_group_user_pkg_group_module` FOREIGN KEY (`id_group`) REFERENCES `pkg_group_user` (`id`),
  ADD CONSTRAINT `fk_pkg_module_pkg_group_module` FOREIGN KEY (`id_module`) REFERENCES `pkg_module` (`id`);

--
-- Constraints for table `pkg_group_user`
--
ALTER TABLE `pkg_group_user`
  ADD CONSTRAINT `pkg_user_type_pkg_group_user` FOREIGN KEY (`id_user_type`) REFERENCES `pkg_user_type` (`id`);

--
-- Constraints for table `pkg_logins`
--
ALTER TABLE `pkg_logins`
  ADD CONSTRAINT `fk_pkg_campaign_billing` FOREIGN KEY (`id_campaign`) REFERENCES `pkg_campaign` (`id`),
  ADD CONSTRAINT `fk_pkg_campaign_pkg_logins` FOREIGN KEY (`id_campaign`) REFERENCES `pkg_campaign` (`id`),
  ADD CONSTRAINT `fk_pkg_operador_billing` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`),
  ADD CONSTRAINT `fk_pkg_operador_pkg_logins` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);

--
-- Constraints for table `pkg_module`
--
ALTER TABLE `pkg_module`
  ADD CONSTRAINT `fk_pkg_module_pkg_module` FOREIGN KEY (`id_module`) REFERENCES `pkg_module` (`id`);

--
-- Constraints for table `pkg_phonebook`
--
ALTER TABLE `pkg_phonebook`
  ADD CONSTRAINT `fk_pkg_trunk_pkg_phonebook` FOREIGN KEY (`id_trunk`) REFERENCES `pkg_trunk` (`id`);

--
-- Constraints for table `pkg_phonenumber`
--
ALTER TABLE `pkg_phonenumber`
  ADD CONSTRAINT `fk_pkg_category_pkg_phonenumber` FOREIGN KEY (`id_category`) REFERENCES `pkg_category` (`id`),
  ADD CONSTRAINT `fk_pkg_phonebook_pkg_phonenumber` FOREIGN KEY (`id_phonebook`) REFERENCES `pkg_phonebook` (`id`),
  ADD CONSTRAINT `fk_pkg_user_pkg_phonenumber` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);

--
-- Constraints for table `pkg_queue`
--
ALTER TABLE `pkg_queue`
  ADD CONSTRAINT `fk_pkg_user_pkg_queue` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);

--
-- Constraints for table `pkg_queue_member`
--
ALTER TABLE `pkg_queue_member`
  ADD CONSTRAINT `fk_pkg_user_pkg_queue_member` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);

--
-- Constraints for table `pkg_sip`
--
ALTER TABLE `pkg_sip`
  ADD CONSTRAINT `fk_pkg_user_pkg_sip` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);

--
-- Constraints for table `pkg_trunk`
--
ALTER TABLE `pkg_trunk`
  ADD CONSTRAINT `fk_pkg_provider_pkg_trunk` FOREIGN KEY (`id_provider`) REFERENCES `pkg_provider` (`id`);

--
-- Constraints for table `pkg_user`
--
ALTER TABLE `pkg_user`
  ADD CONSTRAINT `fk_pkg_user_pkg_group_user` FOREIGN KEY (`id_group`) REFERENCES `pkg_group_user` (`id`);

--
-- Constraints for table `pkg_user_asistencia`
--
ALTER TABLE `pkg_user_asistencia`
  ADD CONSTRAINT `fk_pkg_asistencia_pkg_user_asistencia` FOREIGN KEY (`id_asistencia`) REFERENCES `pkg_asistencia` (`id`),
  ADD CONSTRAINT `fk_pkg_user_pkg_user_asistencia` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);

--
-- Constraints for table `pkg_user_campaign`
--
ALTER TABLE `pkg_user_campaign`
  ADD CONSTRAINT `fk_pkg_campaign_pkg_user_campaign` FOREIGN KEY (`id_campaign`) REFERENCES `pkg_campaign` (`id`),
  ADD CONSTRAINT `fk_pkg_user_pkg_user_campaign` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);

--
-- Constraints for table `pkg_user_online`
--
ALTER TABLE `pkg_user_online`
  ADD CONSTRAINT `fk_pkg_campaign_pkg_user_online` FOREIGN KEY (`id_campaign`) REFERENCES `pkg_campaign` (`id`),
  ADD CONSTRAINT `fk_pkg_operador_pkg_user_online` FOREIGN KEY (`id_user`) REFERENCES `pkg_user` (`id`);
