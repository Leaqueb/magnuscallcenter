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
 * Please submit bug reports, patches, etc to https://github.com/magnussolution/callcenter/issues
 * =======================================
 * Magnusbilling.com <info@magnusbilling.com>
 *
 */
class MagnusCommand extends CConsoleCommand
{
    public $config;
    public $directory = '/var/www/html/callcenter/protected/commands/';

    public function run($args)
    {
        define('LOGFILE', 'protected/runtime/magnus.log');
        define('DEBUG', 0);

        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGHUP, SIG_IGN);
        }

        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

        $agi = new AGI();

        $agi->verbose("Start MagnusCallcenter AGI");

        $MAGNUS = new Magnus();

        $MAGNUS->get_agi_request_parameter($agi);
        if ($agi->get_variable("MEMBERNAME", true)) {

            $operator = preg_replace("/SIP\//", "", $agi->get_variable("MEMBERNAME", true));

            $agi->verbose($agi->get_variable("UNIQUEID", true), 8);
            $agi->verbose($agi->get_variable("MEMBERNAME", true), 8);
            $agi->verbose($agi->get_variable("PHONENUMBER_ID", true), 8);

            $MAGNUS->uniqueid = $agi->get_variable("UNIQUEID", true);
            $MAGNUS->dnid     = $agi->get_variable("CALLED", true);

            Predictive::model()->updateAll(
                array('operador' => $operator),
                'uniqueid = :key',
                array(':key' => $MAGNUS->uniqueid)
            );

            $modelUser                         = User::model()->find("username = :operator", array(':operator' => $operator));
            $modelUser->auto_load_phonenumber  = 1;
            $modelUser->id_current_phonenumber = $agi->get_variable("PHONENUMBER_ID", true);
            $modelUser->save();

            $agi->verbose(date("Y-m-d H:i:s") . " => " . $MAGNUS->dnid . " $operator ATENDEU A CHAMADAS\n\n", 1);

            if ($MAGNUS->agiconfig['record_call'] == 1 || $MAGNUS->record_call == 1) {
                $date  = date("dmY");
                $myres = $agi->execute("MixMonitor " . $MAGNUS->config['global']['record_patch'] . "/{$date}/{$MAGNUS->dnid}.{$MAGNUS->uniqueid}.gsm,b");
                $agi->verbose("MixMonitor " . $MAGNUS->config['global']['record_patch'] . "/{$date}/{$MAGNUS->dnid}.{$MAGNUS->uniqueid}.gsm,b", 3);
            }

            exit;
        } elseif ($agi->get_variable("MASSIVE_CALL", true)) {
            $agi->verbose('MASSIVE CALL', 5);
            MassiveCall::processCall($agi, $MAGNUS, $Calc);
        }

        if (($agi->get_variable("PHONENUMBER_ID", true) > 0 &&
            $agi->get_variable("CAMPAIGN_ID", true) > 0) || strlen($argv[1]) > 1) {

            $mode                   = 'massive-call';
            $MAGNUS->id_phonenumber = $agi->get_variable("PHONENUMBER_ID", true);
        } else {

            /*check if did call*/
            $mydnid = $MAGNUS->dnid;

            $sql = "SELECT * FROM pkg_did_destination WHERE activated=1 AND did LIKE '$mydnid' ";
            $agi->verbose($sql, 25);
            $result_did = Yii::app()->db->createCommand($sql)->queryAll();

            if (count($result_did) > 0) {
                switch ($result_did[0]['voip_call']) {
                    case 1:
                        $mode      = 'Call to Operator';
                        $modelUser = User::model()->findByPk((int) $result_did[0]['id_user']);
                        $dialstr   = 'SIP/' . $modelUser->username;
                        $agi->verbose("DIAL $dialstr", 6);
                        $myres        = $MAGNUS->run_dial($agi, $dialstr);
                        $answeredtime = $agi->get_variable("ANSWEREDTIME");
                        $answeredtime = $answeredtime['data'];
                        $dialstatus   = $agi->get_variable("DIALSTATUS");
                        $dialstatus   = $dialstatus['data'];
                        break;
                    case 2:
                        $mode = 'ivr';
                        break;
                    case 3:
                        //callingcard
                        break;
                    case 4:
                        $mode = 'portalDeVoz';
                        break;
                    case 5:
                        $agi->verbose('RECEIVED ANY CALLBACK', 5);
                        Callback::callbackCID($agi, $MAGNUS, $Calc, $result_did);
                        break;
                    case 6:
                        $agi->verbose('RECEIVED 0800 CALLBACK', 5);
                        Callback::callback0800($agi, $MAGNUS, $Calc, $result_did);
                        break;
                    case 7:
                        $mode = 'queue';
                        break;
                    case 8:
                        $mode = 'callgroup';
                        break;
                    case 9:
                        $mode    = 'custom';
                        $dialstr = $result_did[0]['destination'];
                        $agi->verbose("DIAL $dialstr", 6);
                        $myres        = $MAGNUS->run_dial($agi, $dialstr);
                        $answeredtime = $agi->get_variable("ANSWEREDTIME");
                        $answeredtime = $answeredtime['data'];
                        $dialstatus   = $agi->get_variable("DIALSTATUS");
                        $dialstatus   = $dialstatus['data'];
                        break;
                    default:
                        $mode = 'did';
                        break;
                }
            } else {
                $mode = 'standard';
            }

        }

        $Calc = new Calc();
        $Calc->init();

        if ($mode == 'standard') {
            $agi->verbose('Authenticate', 25);
            $cia_res = Authenticate::authenticateUser($agi, $MAGNUS);

            if ($MAGNUS->id_phonenumber == 0) {
                $agi->verbose('Operator ' . $MAGNUS->accountcode . ' make a direct call to campaign ID ' . $MAGNUS->id_campaign, 8);
            }

            $status_channel = 4;

            $agi->verbose("TRY : callingcard_ivr_authenticate - result $cia_res]", 20);

            /* CALL AUTHENTICATE AND WE HAVE ENOUGH CREDIT TO GO AHEAD */
            if ($cia_res == true) {

                $Calc->init();
                $MAGNUS->init();

                $stat_channel = $agi->channel_status($MAGNUS->channel);

                /* CHECK IF THE CHANNEL IS UP*/
                if (($MAGNUS->agiconfig['answer_call'] == 1) && ($stat_channel["result"] != $status_channel)) {
                    $agi->verbose("STOP - EXIT", 5);
                    $agi->conn = null;
                    $MAGNUS->hangup($agi);
                    exit();
                }

                $MAGNUS->uniqueid = $MAGNUS->uniqueid + 1000000000;

                $MAGNUS->extension = $MAGNUS->dnid;

                $agi->verbose($MAGNUS->dnid, 0);

                $ans = $MAGNUS->checkNumber($agi, $Calc, $i, true);
                $agi->verbose('ANSWER fct callingcard_ivr authorize:> ' . $ans, 20);

                if ($ans == 1) {
                    /* PERFORM THE CALL*/
                    $agi->verbose('Process call', 20);
                    $Calc->sendCall($MAGNUS, $agi, $MAGNUS->destination);

                    $Calc->updateSystem($MAGNUS, $agi, $MAGNUS->destination);

                }
                $MAGNUS->agiconfig['use_dnid'] = 0;
            } else {
                $agi->verbose("[AUTHENTICATION FAILED (cia_res:" . $cia_res . ")]", 20);
            }
        } elseif ($mode == 'massive-call') {
            $agi->verbose('Call Predictive');
            $cia_res = PredictiveAgi::send($agi, $MAGNUS, $Calc);
        } else if ($mode == 'ivr') {
            $agi->answer();

            $Calc->init();
            $MAGNUS->init();
            if (strlen($mydnid) > 0) {
                $agi->verbose("DID IVR - CallerID=" . $MAGNUS->CallerID . " -> DID=" . $mydnid, 6);
                //$MAGNUS->CallIvr($agi, $Calc, $result_did);
                IvrAgi::callIvr($agi, $MAGNUS, $Calc, $result_did);

            }
        } else if ($mode == 'queue') {
            $agi->verbose("DIAL queue", 6);
            $insertCDR = false;
            Queue::callQueue($agi, $MAGNUS, $Calc, $result_did, $type);
        }
        $MANGUS->hangup($agi);
    }
}
