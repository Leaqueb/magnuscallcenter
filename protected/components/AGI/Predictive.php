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

class Predictive
{
    public function send($agi, &$MAGNUS, &$Calc)
    {
        $agi->verbose("[Type Call Predictive]",3);
        $agi->verbose(date("Y-m-d H:i:s") ." => $MAGNUS->dnid, Cliente Atendeu a chamada ",2);

       // $MAGNUS->config['global']['amd'] = 1;
        if (preg_match("/82464731/", $MAGNUS->dnid)) {
            $MAGNUS->config['global']['amd']  = 0;
        }
        if ($MAGNUS->config['global']['amd'] == 1) {
            $agi->execute("AMD");
        }else{
           $agi->verbose( "AMD " .$MAGNUS->config['global']['amd'],3); 
        }
       

        

                     


        $sql = "SELECT * FROM pkg_campaign WHERE id = ".$agi->get_variable("CAMPAIGN_ID", true); 
        $campaignResult = Yii::app()->db->createCommand( $sql )->queryAll();
        $agi->verbose($sql,25);

        $agi->verbose("[CAMPAIGN NAME". $campaignResult[0]['name']." $MAGNUS->uniqueid",20);

        $sql = "INSERT INTO pkg_predictive VALUES ('', '".$MAGNUS->uniqueid."', '".$agi->get_variable("PHONENUMBER_ID", true)."', NULL)";
        Yii::app()->db->createCommand( $sql )->execute();
        $agi->verbose( $sql,25);
        
        $startTime = strtotime("now");

        $agi->set_variable("CALLERID(num)",$agi->get_variable("CALLED", true));
        $agi->set_callerid($agi->get_variable("CALLED", true));

        $agi->verbose('Predictive - Send call to Campaign '. $campaignResult[0]['name'], 5);
        
        //SET uniqueid para ser atualizado a tabela pkg_predictive quando a ligação for atendida
        $agi->set_variable("UNIQUEID",$MAGNUS->uniqueid);
        
        if ($MAGNUS->config['global']['amd'] == 1) {
            $amd_status =  $agi->get_variable("AMDSTATUS", true);
            if (!preg_match("/HUMAN/", $amd_status) ){
                    $agi->verbose(date("Y-m-d H:i:s") ." => ". $MAGNUS->dnid.': amd_status ' .$amd_status.", hangup call",1);
                    $agi->hangup();
                    exit;
            }else{
                $agi->verbose(date("Y-m-d H:i:s") ." => ". $MAGNUS->dnid.': amd_status ' .$amd_status.", send call to agent\n",3);
            }
        }

        //calcula o tempo que gastou para atender o numero
        $ringing_time = time() - $agi->get_variable("STARTCALL", true); 
        $sql = "UPDATE pkg_preditive_gen SET ringing_time = $ringing_time WHERE uniqueID = '".$agi->get_variable("ALEARORIO", true)."'";
        $agi->verbose( $sql,25);
        Yii::app()->db->createCommand( $sql )->execute();

        $agi->verbose(date("Y-m-d H:i:s") ." => $MAGNUS->dnid, enviado para queue ".$campaignResult[0]['name'],1);
        
        $agi->execute("Queue",$campaignResult[0]['name'].',,,,60,/var/www/html/callcenter/agi.php');


        if ($MAGNUS->agiconfig['record_call'] == 1 || $MAGNUS->record_call == 1)
        {
            $myres = $agi->execute("StopMixMonitor");
            $agi->verbose("EXEC StopMixMonitor (" . $MAGNUS->uniqueid . ")",5);
            if (file_exists("".$MAGNUS->config['global']['record_patch']."/".date('dmY')."/".$MAGNUS->dnid.".".$MAGNUS->uniqueid.".gsm")) {
                if ( !is_dir("".$MAGNUS->config['global']['record_patch']."/".date('dmY') ) ) {
                   exec("mkdir ".$MAGNUS->config['global']['record_patch']."/".date('dmY'));
                }
                $agi->verbose("mv ".$MAGNUS->config['global']['record_patch']."/".date('dmY')."/".$MAGNUS->dnid.".".$MAGNUS->uniqueid.".gsm ".$MAGNUS->config['global']['record_patch']."/".date('dmY')."/");
                
                exec("mv ".$MAGNUS->config['global']['record_patch']."/".date('dmY')."/".$MAGNUS->dnid.".".$MAGNUS->uniqueid.".gsm ".$MAGNUS->config['global']['record_patch']."/".date('dmY')."/");
                
            }
        }


        $agi->verbose(date("Y-m-d H:i:s") ." => $MAGNUS->dnid, ".$MAGNUS->uniqueid." DELIGOU A CHAMADAS",1);

           
        $endTime = strtotime("now");              

        $Calc->answeredtime = $Calc->real_answeredtime = $endTime - $startTime;
        
        //pega o usuario que atendeu a chamada              

        $sql = "SELECT id FROM pkg_user WHERE username = (SELECT operador FROM pkg_predictive WHERE uniqueid = '".$MAGNUS->uniqueid."')"; 
        $userResult = Yii::app()->db->createCommand( $sql )->queryAll();
        $agi->verbose( $sql,25);
        
        $MAGNUS->id_user = $userResult[0]['id'];

        $Calc->usedtrunk                             = $agi->get_variable("IDTRUNK", true);
        $Calc->tariffObj[0]['id_campaign_number'] = $agi->get_variable("CAMPAIGN_ID", true);

        $Calc->tariffObj[0]['id_phonebook']       = $agi->get_variable("IDPHONEBOOK", true);
        $Calc->tariffObj[0]['id']                 = $agi->get_variable("PHONENUMBER_ID", true);

        $MAGNUS->dnid = $agi->get_variable("CALLERID", true);
        $terminatecauseid = $Calc->answeredtime > 0 ? 1 : 0;
        $Calc->updateSystem($MAGNUS, $agi, $MAGNUS->dnid, $terminatecauseid);
    }

}