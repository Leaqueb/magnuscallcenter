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
 
class Authenticate
{
    public function authenticateUser($agi, $MAGNUS)
    {
        $MAGNUS->username = $MAGNUS->cardnumber = $MAGNUS->accountcode;
        if ($MAGNUS->accountcode=='unknown') {
            $agi->verbose('Sem accountcode ' .$MAGNUS->accountcode. ' '.$agi->request['agi_callerid'],25);
            $MAGNUS->username = $MAGNUS->cardnumber = $MAGNUS->accountcode = $agi->request['agi_callerid']; 
        }

        $modelUser = User::model()->find("username = :accountcode", array(':accountcode'=>$MAGNUS->accountcode));


        if (count($modelUser) && $modelUser->id_campaign > 0)
        {
            $modelOperatorStatus = OperatorStatus::model()->find("id_user = :id_user AND queue_paused = 1", array(':id_user'=>$modelUser->id));
            if(count($modelOperatorStatus)){

                if ($modelOperatorStatus->categorizing && $modelUser->id_current_phonenumber > 0) {
                    //verifico se esta tentando ligar para o mesmo numero
                    $modelPhoneNumber = PhoneNumber::model()->findByPk($modelUser->id_current_phonenumber);
                    if (count($modelPhoneNumber) && $modelPhoneNumber->number != $MAGNUS->dnid) {
                        $agi->answer();
                        $agi->verbose('OPERATOR TRY CALL TO ANOTHER NUMBER BUT HE IS CATEGORIZING',1);
                        $agi->stream_file('prepaid-invalid-digits', '#');
                        $MAGNUS->hangup($agi);   
                        exit;
                    }
                }else{
                    $agi->answer();
                    $agi->verbose('USER IS IN PAUSE',1);
                    $agi->stream_file('prepaid-in-pause', '#'); 
                    $MAGNUS->hangup($agi);   
                    exit;
                }

                
            }
            $MAGNUS->id_campaign = $modelUser->id_campaign;     
            $MAGNUS->status      = true;
            $MAGNUS->id_user     = $modelUser->id; 
            $MAGNUS->username    = $modelUser->username; 
            $authentication      = true;  
            return true;      
        } 
        else
        {
            $agi->verbose('prepaid-is-not-login',5);
            $agi->stream_file('prepaid-is-not-login', '#');      
            $MAGNUS->hangup($agi);   
            exit;
        }
    }

};
