<?php
/**
 * Acoes do modulo "Campaign".
 *
 * MagnusSolution.com <info@magnussolution.com>
 * 28/10/2012
 */

class CampaignController extends BaseController
{
    public $attributeOrder     = 't.id';
    public $nameModelRelated   = 'CampaignPhonebook';
    public $nameFkRelated      = 'id_campaign';
    public $nameOtherFkRelated = 'id_phonebook';

    public function init()
    {
        $this->instanceModel        = new Campaign;
        $this->abstractModel        = Campaign::model();
        $this->abstractModelRelated = CampaignPhonebook::model();
        $this->titleReport          = Yii::t('yii', 'Campaign');

        parent::init();
    }

    public function extraFilterCustom($filter)
    {
        $filter .= ' AND id > 0';

        $filter .= !preg_match("/status/", $filter) ? ' AND status = 1' : false;

        if (Yii::app()->session['isOperator']) {
            $filter = $this->extraFilterCustomOperator($filter);
        } else if (Yii::app()->session['isClient']) {
            $filter = $this->extraFilterCustomClient($filter);
        }

        return $filter;
    }

    public function actionRead($asJson = true, $condition = null)
    {
        //get the campaigns availables to operator login
        if (Yii::app()->session['isOperator'] == true) {
            $this->join = 'JOIN pkg_user_campaign uc ON t.id = uc.id_campaign';
        }

        parent::actionRead();
    }

    public function actionLoginOut()
    {
        $id_campaign = $_POST['id'];

        $predictive = false;

        $currentyTime = date('H:i:s');

        $modelUser = User::model()->findByPk((int) Yii::app()->session['id_user']);

        if ($modelUser->break_mandatory == 1) {
            echo json_encode(array(
                $this->nameSuccess => false,
                $this->nameMsg     => 'Voce esta em pausa obrigatoria',
                'predictive'       => $predictive,
            ));
            exit;
        }

        //id_campaign is the name, becous I cannot get the combo key, only the value on Extjs
        if (is_numeric($id_campaign)) {
            $modelCampaign = Campaign::model()->findByPk((int) $id_campaign);
        } else {
            $modelCampaign = Campaign::model()->find("name = :campaign_name", array(":campaign_name" => $id_campaign));
        }

        $turno = Util::detectTurno($modelCampaign);

        //faz login do operador quando for uma campanha que nao usa o sistema
        if ($_POST['action'] == 'login') {

            $modelUser->id_campaign = $id_campaign;
            $modelUser->save();

            $modelOperatorStatus               = new OperatorStatus();
            $modelOperatorStatus->id_user      = Yii::app()->session['id_user'];
            $modelOperatorStatus->in_call      = 0;
            $modelOperatorStatus->queue_status = 0;
            $modelOperatorStatus->time_free    = time();
            $modelOperatorStatus->id_campaign  = $modelUser->id_campaign;
            try {
                $modelOperatorStatus->save();
            } catch (Exception $e) {

            }

            AsteriskAccess::instance()->queueAddMember(Yii::app()->session['username'], $modelCampaign->name);

            $modelLoginsCampaign              = new LoginsCampaign();
            $modelLoginsCampaign->id_user     = Yii::app()->session['id_user'];
            $modelLoginsCampaign->id_campaign = $modelCampaign->id;
            $modelLoginsCampaign->total_time  = 0;
            $modelLoginsCampaign->starttime   = date('Y-m-d H:i:s');
            $modelLoginsCampaign->turno       = $turno;
            $modelLoginsCampaign->login_type  = 'LOGIN';
            $modelLoginsCampaign->id_breaks   = null;
            $modelLoginsCampaign->save();

            /*
            $status = 'Not in use';
            $values = "'".Yii::app()->session['id_user']."', '".$id_campaign."', '$status','','',''";
            $fields = "id_user, id_campaign, status,codec,duration,reinvite";
            $sql = "INSERT INTO pkg_call_online ( $fields )VALUES ($values)";
            try {
            @Yii::app()->db->createCommand($sql)->execute();
            } catch (Exception $e) {

            }
             */

            $msg = Yii::t('yii', 'Usted esta logueado en la campaña ') . $modelCampaign->name;

            Yii::app()->session['id_campaign']   = $modelCampaign->id;
            Yii::app()->session['campaign_name'] = $modelCampaign->name;
            /*if ($resultNameCampaign[0]['predictive'] == 1)
        $predictive = true;*/

        } elseif ($_POST['action'] == 'logout') {

            //$turno = $this->detectTurno($resultNameCampaign,$currentyTime);
            AsteriskAccess::instance()->queueRemoveMember(Yii::app()->session['username'], $modelCampaign->name);

            /*
            if ($modelUser->training == 1) {

            $sql = "SELECT turno, starttime FROM pkg_logins_campaign WHERE id_user = ".Yii::app()->session['id_user']." AND stoptime = '0000-00-00 00:00:00'  AND id_campaign = ".$resultNameCampaign[0]['id'];
            $userOnlineResult = Yii::app()->db->createCommand($sql)->queryAll();

            $sql = "DELETE FROM pkg_logins_campaign WHERE id_user = ".Yii::app()->session['id_user']." AND stoptime = '0000-00-00 00:00:00'  AND id_campaign = ".$resultNameCampaign[0]['id'];
            Yii::app()->db->createCommand($sql)->execute();

            $sql  = "SELECT config_value FROM pkg_configuration WHERE config_key = 'valor_hora'";
            $configResult   = Yii::app()->db->createCommand($sql)->queryAll();
            $valorSegundo = $configResult[0]['config_value'] / 3600;

            $totalTimePause = strtotime(date('Y-m-d H:i:s')) - strtotime ($userOnlineResult[0]['starttime']) ;

            $totalPricePause = number_format($totalTimePause * $valorSegundo,2);

            $sql = "INSERT INTO pkg_billing (id_user, id_campaign, `date`, total_price, total_time, turno) VALUES ( ".Yii::app()->session['id_user'].", '-4', '".date('Y-m-d')."', '$totalPricePause', '$totalTimePause', '".$userOnlineResult[0]['turno']."')";
            Yii::app()->db->createCommand($sql)->execute();

            }*/

            $modelPhonenumber = PhoneNumber::model()->findByPk($modelUser->id_current_phonenumber);
            if (count($modelPhonenumber)) {
                $modelPhonenumber->id_user = null;
                $modelPhonenumber->save();
            }

            $modelUser->id_current_phonenumber = null;
            $modelUser->id_campaign            = null;
            $modelUser->save();

            $modelLoginsCampaign = LoginsCampaign::model()->find(
                "id_user = :id_user AND stoptime = :stoptime AND
                    id_campaign = :id_campaign AND login_type = :login_type",
                array(
                    ":id_campaign" => $modelCampaign->id,
                    ":id_user"     => Yii::app()->session['id_user'],
                    ":stoptime"    => '0000-00-00 00:00:00',
                    ":login_type"  => 'LOGIN',
                ));

            $modelLoginsCampaign->stoptime   = date('Y-m-d H:i:s');
            $modelLoginsCampaign->total_time = strtotime(date('Y-m-d H:i:s')) - strtotime($modelLoginsCampaign->starttime);
            try {
                $modelLoginsCampaign->save();
            } catch (Exception $e) {
                Yii::log(print_r($modelLoginsCampaign->errors, true), 'info');
            }

            OperatorStatus::model()->deleteAll("id_user = " . Yii::app()->session['id_user']);

            Yii::app()->session['id_campaign'] = null;

            $msg = Yii::t('yii', 'Usted se deslogueo con exito');
        } elseif ($_POST['action'] == 'pause') {

            $modelOperatorStatus = OperatorStatus::model()->find("id_user = " . Yii::app()->session['id_user']);
            if ($modelOperatorStatus->categorizing == 1) {
                $msg = Yii::t('yii', 'Please categorize the number before break');
                echo json_encode(array(
                    $this->nameSuccess => false,
                    $this->nameMsg     => $msg,
                ));
                exit;
            }

            AsteriskAccess::instance()->queuePauseMember(Yii::app()->session['username'], $modelCampaign->name);

            $modelLoginsCampaign              = new LoginsCampaign();
            $modelLoginsCampaign->id_user     = Yii::app()->session['id_user'];
            $modelLoginsCampaign->id_campaign = $modelCampaign->id;
            $modelLoginsCampaign->total_time  = 0;
            $modelLoginsCampaign->starttime   = date('Y-m-d H:i:s');
            $modelLoginsCampaign->turno       = $turno;
            $modelLoginsCampaign->login_type  = 'PAUSE';
            $modelLoginsCampaign->id_breaks   = $_POST['id_breaks'];

            $modelLoginsCampaign->save();

            $msg = 'Usted entro en descanso con exito';

        } elseif ($_POST['action'] == 'unpause') {

            $id_user = isset($_POST['id_user']) ? $_POST['id_user'] : Yii::app()->session['id_user'];

            /*$sql = "SELECT * FROM pkg_call_online WHERE id_user = :id_user";
            $command = Yii::app()->db->createCommand($sql);
            $command->bindValue(":id_user", $id_user, PDO::PARAM_INT);
            $resultCAtegorizando = $command->queryAll();    */

            /*if ($resultNameCampaign[0]['predictive'] == 1)
            $predictive = true;*/

            //nao estava categorizando
            //if ($resultCAtegorizando[0]['categorizando'] == 0) {

            $modelLoginsCampaign = LoginsCampaign::model()->find(
                "id_user = :id_user AND stoptime = :stoptime AND
                        id_campaign = :id_campaign AND login_type = :login_type",
                array(
                    ":id_campaign" => $modelCampaign->id,
                    ":id_user"     => $id_user,
                    ":stoptime"    => '0000-00-00 00:00:00',
                    ":login_type"  => 'PAUSE',
                ));

            if ($modelLoginsCampaign->idBreak->mandatory and
                $modelLoginsCampaign->idBreak->stop_time > date('H:i:s')) {
                $msg = Yii::t('yii', 'You cannot unbreak becouse this break is mandatory');
                echo json_encode(array(
                    $this->nameSuccess => false,
                    $this->nameMsg     => $msg,
                ));
                exit;
            }

            if (Yii::app()->session['isOperator']) {

                $modelBreaks = Breaks::model()->findByPk((int) $modelLoginsCampaign->id_breaks);
                $deadline    = $modelBreaks->maximum_time;

                if (strtotime($modelLoginsCampaign->starttime . ' +' . $modelBreaks->maximum_time . ' minutes') < strtotime('now')) {

                    echo json_encode(array(
                        $this->nameSuccess => false,
                        $this->nameMsg     => Yii::t('yii', 'Pause time is over, call the supervisor to unlock the system'),
                    ));
                    exit();

                }

                AsteriskAccess::instance()->queueUnPauseMember(Yii::app()->session['username'], $modelCampaign->name);
            }

            $modelLoginsCampaign->stoptime   = date('Y-m-d H:i:s');
            $modelLoginsCampaign->total_time = strtotime(date('Y-m-d H:i:s')) - strtotime($modelLoginsCampaign->starttime);
            try {
                $modelLoginsCampaign->save();
            } catch (Exception $e) {
                Yii::log(print_r($modelLoginsCampaign->errors, true), 'info');
            }

            $modelOperatorStatus = OperatorStatus::model()->find(
                "id_user = " . Yii::app()->session['id_user']);
            $modelOperatorStatus->time_free = time();
            $modelOperatorStatus->save();

            $msg = Yii::t('yii', 'Usted salio del descanso con exito');

            /* }
        else if ($resultCAtegorizando[0]['categorizando'] == 3) {
        $sql = "UPDATE pkg_call_online SET categorizando = 1 WHERE  id_user = ".$id_user." ";
        Yii::app()->db->createCommand($sql)->execute();
        }*/

        }

        echo json_encode(array(
            $this->nameSuccess => true,
            $this->nameMsg     => $msg,
            'predictive'       => $predictive,
        ));
    }

    public function afterSave($model, $values)
    {
        $this->generateQueueFile();
    }

    public function afterDestroy($values)
    {
        $this->generateQueueFile();
    }

    public function generateQueueFile()
    {
        $modelCampaign = Campaign::model()->findAll(
            array(
                'select'    => 'name, musiconhold, strategy, ringinuse, timeout, retry, wrapuptime, weight,
                        `periodic-announce`, `periodic-announce-frequency`, `announce-holdtime`, `announce-position`,
                        `announce-frequency`, joinempty, leavewhenempty, eventmemberstatus, autopause,
                        setqueuevar, setqueueentryvar, setinterfacevar',
                'condition' => "status = 1",
            ));

        if (is_array($modelCampaign) > 0) {
            AsteriskAccess::instance()->writeAsteriskFile($modelCampaign, '/etc/asterisk/queue_magnus.conf', 'name');

        }

    }
}
