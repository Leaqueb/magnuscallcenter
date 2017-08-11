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

class MassiveCall
{
    public function processCall($agi, &$MAGNUS, &$Calc)
    {
        $agi->answer();
        $now       = time();
        $uploaddir = $MAGNUS->magnusFilesDirectory . 'sounds/';

        if ($MAGNUS->dnid == 'failed' || !is_numeric($MAGNUS->dnid)) {
            $agi->verbose("Hangup becouse dnid is OutgoingSpoolFailed", 25);
            $MAGNUS->hangup($agi);
        }

        $id_phonenumber = $agi->get_variable("PHONENUMBER_ID", true);
        $id_campaign    = $agi->get_variable("CAMPAIGN_ID", true);

        $agi->verbose('MASSIVE CALL' . $id_campaign, 5);

        $modelCampaign = MassiveCallCampaign::model()->findByPk((int) $id_campaign);

        $agi->verbose($modelCampaign->id_campaign);

        /*AUDIO FOR CAMPAIN*/
        $audio = $audioDir . "idMassiveCallCampaign_" . $id_campaign;

        $agi->verbose('MASSIVE CALL' . $audio, 5);

        //se tiver audio 2, executar o audio 1 sem esperar DTMF
        if (strlen($modelCampaign->audio_2) > 1) {
            $agi->verbose('Execute audio 1. No DTMF');
            $agi->stream_file($audio, ' #');
        } else {
            $agi->verbose('Execute audio 1 DTMF');
            $res_dtmf = $agi->get_data($audio, 5000, 1);
        }

        if (strlen($modelCampaign->audio_2) > 1) {
            /*Execute audio 2*/
            $audio = $audioDir . "idMassiveCallCampaign_" . $id_campaign . "_2";

            $res_dtmf = $agi->get_data($audio, 5000, 1);

        }

        $agi->verbose('RESULT DTMF ' . $res_dtmf['result'], 25);

        if (strlen($modelCampaign->audio) < 5) {
            $res_dtmf['result'] = $modelCampaign->forward_number;
            $agi->verbose('CAMPAIN SEM AUDIO, ENVIA DIRETO PARA ' . $res_dtmf['result']);
        }

        $agi->verbose("have Forward number $forward_number");

        $agi->set_variable("CALLERID(num)", $destination);

        if ($res_dtmf['result'] == $modelCampaign->forward_number) {
            $result_did                   = array();
            $result_did[0]['did']         = $MAGNUS->dnid;
            $result_did[0]['id_campaign'] = $modelCampaign->id_campaign;
            Queue::callQueue($agi, $MAGNUS, $Calc, $result_did, $type);
        } else {
            $MANGUS->hangup($agi);
        }

    }

}
