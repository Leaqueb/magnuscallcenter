<?php
/**
 * Acoes do modulo "Call".
 *
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
 * 19/09/2012
 */

class OperatorStatusController extends BaseController
{
    public $attributeOrder = 't.id DESC';
    public $extraValues    = array('idUser' => 'username,name', 'idCampaign' => 'name');

    public function init()
    {
        $this->instanceModel = new OperatorStatus;
        $this->abstractModel = OperatorStatus::model();
        $this->titleReport   = Yii::t('yii', 'OperatorStatus');
        parent::init();
    }

    public function setAttributesModels($attributes, $models)
    {
        for ($i = 0; $i < count($attributes) && is_array($attributes); $i++) {

            if ($attributes[$i]['categorizing'] == 1) {
                $attributes[$i]['time_free'] = time() - $attributes[$i]['time_start_cat'];
            } else if ($attributes[$i]['queue_paused'] == 1 && $attributes[$i]['categorizing'] == 0) {
                $modelLoginsCampaign = LoginsCampaign::model()->find(
                    "id_user = " . $attributes[$i]['id_user'] . " AND login_type = 'PAUSE' AND stoptime = '0000-00-00 00:00:00'"
                );
                isset($modelLoginsCampaign->idBreak->name) ? $modelLoginsCampaign->idBreak->name : 'INVALID';
                $attributes[$i]['time_free'] = time() - strtotime($modelLoginsCampaign->starttime);
            } elseif ($attributes[$i]['queue_status'] == 2 || $attributes[$i]['queue_status'] == 6 || $attributes[$i]['queue_status'] == 1) {
                $attributes[$i]['time_free'] = time() - $attributes[$i]['time_free'];
            } else {
                $attributes[$i]['time_free'] = '';
            }

        }
        return $attributes;

    }

    //Verifica o status atual do operadora
    public function actionOperatorCheckStatus()
    {

        $modelOperatorStatus = OperatorStatus::model()->find("id_user = " . Yii::app()->session['id_user']);

        if (count($modelOperatorStatus) > 0) {
            if ($modelOperatorStatus->categorizing == 1) {
                $status = 'CATEGORIZING';
            } else if ($modelOperatorStatus->queue_paused == 1) {
                $status = 'PAUSED';
            } else {
                switch ($modelOperatorStatus->queue_status) {
                    case 0:
                        $status = 'UNKNOWN';
                        break;
                    case 1:
                        $status = 'NOT_INUSE';
                        break;
                    case 2:
                        $status = 'INUSE';
                        break;
                    case 3:
                        $status = 'BUSY';
                        break;
                    case 4:
                        $status = 'INVALID';
                        break;
                    case 5:
                        $status = 'NOT LOGED ON SOFTPHONE';
                        break;
                    case 6:
                        $status = 'RINGING';
                        break;
                    case 7:
                        $status = 'RINGINUSE';
                        break;
                    case 8:
                        $status = 'ONHOLD';
                        break;
                    default:
                        $status = 'UNKNOWN';
                        break;
                }
            }

        } elseif (count($modelOperatorStatus) == 0) {
            $status = 'NO CAMPAING';
        }
        //check if exist a mandaroyBreak
        Yii::import('application.controllers.BreaksController');
        $status = BreaksController::checkMandatoryBreak($status);

        $status = array('rows' => array('status' => $status[0]), 'break_madatory' => $status[1]);

        echo json_encode($status);
    }

}
