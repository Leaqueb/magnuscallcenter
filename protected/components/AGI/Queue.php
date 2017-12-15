<?php
/**
 * =======================================
 * ###################################
 * MagnusBilling
 *
 * @package MagnusBilling
 * @author Adilson Leffa Magnus.
 * @copyright Copyright (C) 2005 - 2016 MagnusBilling. All rights reserved.
 * ###################################
 *
 * This software is released under the terms of the GNU Lesser General Public License v2.1
 * A copy of which is available from http://www.gnu.org/copyleft/lesser.html
 *
 * Please submit bug reports, patches, etc to https://github.com/magnusbilling/mbilling/issues
 * =======================================
 * Magnusbilling.com <info@magnusbilling.com>
 *
 */

class Queue
{
    public function callQueue($agi, &$MAGNUS, &$Calc, $result_did, $type = 'queue')
    {
        $agi->verbose("Queue module", 5);
        $agi->answer();
        $startTime = time();

        $MAGNUS->destination = $MAGNUS->dnid = $result_did[0]['did'];

        $agi->verbose(print_r($result_did, true));

        $sql = "SELECT * FROM pkg_campaign WHERE id =" . $result_did[0]['id_campaign'];
        $agi->verbose($sql, 25);
        $campaignResult = Yii::app()->db->createCommand($sql)->queryAll();

        $nowtime = date('H:s');

        if ($nowtime > $campaignResult[0]['daily_morning_start_time'] &&
            $nowtime < $campaignResult[0]['daily_morning_stop_time']) {
            //echo "turno manha";
        } elseif ($nowtime > $campaignResult[0]['daily_afternoon_start_time'] &&
            $nowtime < $campaignResult[0]['daily_afternoon_stop_time']) {
            //echo "Turno Tarde";
        } else {
            $agi->verbose(' Campanha fora de turno' . $campaignResult[0]['name']);
            $MAGNUS->hangup();
        }

        $sql = "SELECT * FROM pkg_campaign_phonebook WHERE id_campaign = " . $campaignResult[0]['id'];
        $agi->verbose($sql, 25);
        $resultPhoneBook = Yii::app()->db->createCommand($sql)->queryAll();

        if (count($resultPhoneBook) < 1) {
            $agi->verbose(' Campanha sem agenda: ' . $campaignResult[0]['name']);
            $MAGNUS->hangup();
        }

        $sql = "SELECT * FROM pkg_phonenumber WHERE number = '" . $MAGNUS->destination . "' AND
                    id_phonebook IN (SELECT id_phonebook FROM pkg_campaign_phonebook WHERE
                    id_campaign = " . $campaignResult[0]['id'] . ")";
        $agi->verbose($sql, 25);
        $resultPhoneNumber = Yii::app()->db->createCommand($sql)->queryAll();

        if (count($resultPhoneNumber) > 0) {
            $idPhoneNumber = $resultPhoneNumber[0]['id'];
        } else {

            $sql = "INSERT INTO pkg_phonenumber (id_phonebook, number, status, id_category)
                        VALUES (:id_phonebook, :number, 1, 0)";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindValue(":id_phonebook", $resultPhoneBook[0]['id_phonebook'], PDO::PARAM_INT);
            $command->bindValue(":number", $MAGNUS->destination, PDO::PARAM_STR);
            $command->execute();
            $idPhoneNumber = Yii::app()->db->lastInsertID;
        }
        $aleatorio = str_replace(" ", "", microtime(true));

        $sql = "INSERT INTO pkg_predictive VALUES (NULL, '" . $MAGNUS->uniqueid . "', '" . $idPhoneNumber . "', NULL)";
        Yii::app()->db->createCommand($sql)->execute();
        $agi->verbose($sql, 25);

        //salvamos os dados da chamada gerada
        $sql = "INSERT INTO pkg_preditive_gen (date, uniqueID,id_phonebook,ringing_time) VALUES ('" . time() . "', " . $MAGNUS->uniqueid . ", " . $resultPhoneBook[0]['id_phonebook'] . ",0)";
        Yii::app()->db->createCommand($sql)->execute();

        $startTime = strtotime("now");
        $agi->set_variable("CALLERID(num)", $agi->get_variable("CALLED", true));
        $agi->set_callerid($agi->get_variable("CALLED", true));

        $agi->verbose('Receptivo - Send call to Campaign ' . $campaignResult[0]['name'], 5);
        //SET uniqueid para ser atualizado a tabela pkg_predictive quando a ligação for atendida
        $agi->set_variable("UNIQUEID", $MAGNUS->uniqueid);

        $agi->set_variable("CALLERID", $MAGNUS->destination);
        $agi->set_variable("CALLED", $MAGNUS->destination);
        $agi->set_variable("PHONENUMBER_ID", $idPhoneNumber);
        $agi->set_variable("IDPHONEBOOK", $resultPhoneBook[0]['id_phonebook']);
        $agi->set_variable("CAMPAIGN_ID", $campaignResult[0]['id']);
        $agi->set_variable("STARTCALL", time());
        $agi->set_variable("ALEARORIO", $aleatorio);

        $agi->execute("Queue", $campaignResult[0]['name'] . ',,,,60,/var/www/html/callcenter/agi.php');

        if ($MAGNUS->agiconfig['record_call'] == 1 || $MAGNUS->record_call == 1) {
            $myres = $agi->execute("StopMixMonitor");
            $agi->verbose("EXEC StopMixMonitor (" . $MAGNUS->uniqueid . ")", 5);
            if (file_exists("" . $MAGNUS->config['global']['record_patch'] . "/" . date('dmY') . "/" . $MAGNUS->dnid . "." . $MAGNUS->uniqueid . ".gsm")) {
                if (!is_dir("" . $MAGNUS->config['global']['record_patch'] . "/" . date('dmY'))) {
                    exec("mkdir " . $MAGNUS->config['global']['record_patch'] . "/" . date('dmY'));
                }
                $agi->verbose("mv " . $MAGNUS->config['global']['record_patch'] . "/" . date('dmY') . "/" . $MAGNUS->dnid . "." . $MAGNUS->uniqueid . ".gsm " . $MAGNUS->config['global']['record_patch'] . "/" . date('dmY') . "/");

                exec("mv " . $MAGNUS->config['global']['record_patch'] . "/" . date('dmY') . "/" . $MAGNUS->dnid . "." . $MAGNUS->uniqueid . ".gsm " . $MAGNUS->config['global']['record_patch'] . "/" . date('dmY') . "/");

            }
        }

        $agi->verbose(date("Y-m-d H:i:s") . " => $MAGNUS->dnid, " . $MAGNUS->uniqueid . " DELIGOU A CHAMADAS", 1);

        $endTime = strtotime("now");

        $Calc->answeredtime = $Calc->real_answeredtime = $endTime - $startTime;

        //pega o usuario que atendeu a chamada

        $sql        = "SELECT id FROM pkg_user WHERE username = (SELECT operador FROM pkg_predictive WHERE uniqueid = '" . $MAGNUS->uniqueid . "')";
        $userResult = Yii::app()->db->createCommand($sql)->queryAll();
        $agi->verbose($sql, 25);

        $MAGNUS->id_user = $userResult[0]['id'];

        $trunk       = explode("-", substr($MAGNUS->channel, 4));
        $sql         = "SELECT id FROM pkg_trunk WHERE trunkcode LIKE '" . $trunk[0] . "'";
        $trunkResult = Yii::app()->db->createCommand($sql)->queryAll();

        $Calc->usedtrunk                          = $trunkResult[0]['id'];
        $Calc->tariffObj[0]['id_campaign_number'] = $campaignResult[0]['id'];

        $Calc->tariffObj[0]['id_phonebook'] = $resultPhoneBook[0]['id_phonebook'];
        $Calc->tariffObj[0]['id']           = $idPhoneNumber;

        $terminatecauseid = $Calc->answeredtime > 0 ? 1 : 0;
        $Calc->updateSystem($MAGNUS, $agi, $MAGNUS->dnid, $terminatecauseid);
    }

    public function queueMassivaCall($agi, &$MAGNUS, &$Calc, $modelCampaign)
    {
        $agi->verbose("Queue module", 5);
        $agi->answer();
        $startTime = time();

        $agi->verbose(print_r($result_did, true));

        $startTime = strtotime("now");
        $agi->set_variable("CALLERID(num)", $agi->get_variable("CALLED", true));
        $agi->set_callerid($agi->get_variable("CALLED", true));

        $modelCampainForward = Campaign::model()->findByPk($modelCampaign->id_campaign);

        $agi->verbose('Receptivo - Send call to Campaign ' . $modelCampainForward->name, 5);
        //SET uniqueid para ser atualizado a tabela pkg_predictive quando a ligação for atendida
        $agi->set_variable("UNIQUEID", $MAGNUS->uniqueid);

        $agi->set_variable("CALLERID", $MAGNUS->dnid);
        $agi->set_variable("CALLED", $MAGNUS->dnid);
        $agi->set_variable("PHONENUMBER_ID", $idPhoneNumber);
        $agi->set_variable("IDPHONEBOOK", $agi->get_variable("PHONENUMBER_ID", true));
        $agi->set_variable("CAMPAIGN_ID", $modelCampainForward->id);
        $agi->set_variable("STARTCALL", time());
        $agi->set_variable("ALEARORIO", $aleatorio);

        $agi->execute("Queue", $modelCampainForward->name . ',,,,60,/var/www/html/callcenter/agi.php');

        //$Calc->updateSystem($MAGNUS, $agi, $MAGNUS->dnid, $terminatecauseid);
    }
}
