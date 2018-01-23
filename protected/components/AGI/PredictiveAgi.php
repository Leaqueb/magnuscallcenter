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

class PredictiveAgi
{
    public function send($agi, &$MAGNUS, &$Calc)
    {
        $agi->verbose("[Type Call Predictive]", 3);
        $agi->verbose(date("Y-m-d H:i:s") . " => $MAGNUS->dnid, Cliente Atendeu a chamada, campanha " . $agi->get_variable("CAMPAIGN_ID", true), 2);

        if ($MAGNUS->config['agi-conf1']['amd'] == 1) {
            $agi->execute("AMD");
        }

        $modelCampaign = Campaign::model()->findByPk((int) $agi->get_variable("CAMPAIGN_ID", true));
        $agi->verbose("[CAMPAIGN NAME " . $modelCampaign->name . " " . $MAGNUS->uniqueid, 20);

        $modelPredictive           = new Predictive();
        $modelPredictive->number   = $agi->get_variable("PHONENUMBER_ID", true);
        $modelPredictive->uniqueid = $MAGNUS->uniqueid;
        $modelPredictive->save();

        $startTime = time();
        $callerID  = $agi->get_variable("CALLED", true);
        $agi->set_callerid($callerID);
        $agi->set_variable("CALLERID(num)", $callerID);
        $agi->set_variable("CALLERID(all)", "$callerID < >");

        $agi->verbose('Predictive - Send call to Campaign ' . $modelCampaign->name, 5);

        //SET uniqueid para ser atualizado a tabela pkg_predictive quando a ligação for atendida
        $agi->set_variable("UNIQUEID", $MAGNUS->uniqueid);

        if ($MAGNUS->config['agi-conf1']['amd'] == 1) {
            $amd_status = $agi->get_variable("AMDSTATUS", true);
            if (!preg_match("/HUMAN/", $amd_status)) {
                $agi->verbose(date("Y-m-d H:i:s") . " => " . $MAGNUS->dnid . ': amd_status ' . $amd_status . ", hangup call", 1);
                $agi->hangup();
                exit;
            } else {
                $agi->verbose(date("Y-m-d H:i:s") . " => " . $MAGNUS->dnid . ': amd_status ' . $amd_status . ", send call to agent\n", 3);
            }
        }
        //calcula o tempo que gastou para atender o numero
        $ringing_time = $startTime - $agi->get_variable("STARTCALL", true);
        $agi->verbose($agi->get_variable("ALEARORIO", true));

        PredictiveGen::model()->updateAll(
            array('ringing_time' => $ringing_time),
            'uniqueID = :key',
            array(':key' => $agi->get_variable("ALEARORIO", true))
        );

        $agi->verbose(date("Y-m-d H:i:s") . " => $MAGNUS->dnid, enviado para queue " . $modelCampaign->name, 1);

        $agi->execute("Queue", $modelCampaign->name . ',,,,60,/var/www/html/callcenter/agi.php');

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

        $endTime = time();

        $Calc->answeredtime = $Calc->real_answeredtime = $endTime - $startTime;

        //pega o usuario que atendeu a chamada
        $modelUser = User::model()->find('username = (SELECT operador FROM pkg_predictive WHERE uniqueid = :key)',
            array(':key' => $MAGNUS->uniqueid));

        $agi->verbose('id_user ' . $modelUser->id, 25);

        $MAGNUS->id_user = $modelUser->id;

        $Calc->usedtrunk                          = $agi->get_variable("IDTRUNK", true);
        $Calc->tariffObj[0]['id_campaign_number'] = $agi->get_variable("CAMPAIGN_ID", true);

        $Calc->tariffObj[0]['id_phonebook'] = $agi->get_variable("IDPHONEBOOK", true);
        $Calc->tariffObj[0]['id']           = $agi->get_variable("PHONENUMBER_ID", true);

        $MAGNUS->dnid     = $agi->get_variable("CALLERID", true);
        $terminatecauseid = $Calc->answeredtime > 0 ? 1 : 0;
        $Calc->updateSystem($MAGNUS, $agi, $MAGNUS->dnid, $terminatecauseid);
    }

}
