<?php
class TurnosCkeckCommand extends ConsoleCommand
{

    public function run($args)
    {

        include_once "/var/www/html/callcenter/protected/commands/AGI.Class.php";
        $asmanager = new AGI_AsteriskManager;
        $asmanager->connect('localhost', 'magnus', 'magnussolution');

        sleep(40);

        $sql              = "SELECT * FROM pkg_logins_campaign WHERE stoptime = '0000-00-00 00:00:00'";
        $userOnlineResult = Yii::app()->db->createCommand($sql)->queryAll();

        foreach ($userOnlineResult as $key => $userOnline) {

            $currentyTime = date('H:i:s');
            echo "CurrentTime $currentyTime \n";

            $sql            = "SELECT * FROM pkg_campaign WHERE id =" . $userOnline['id_campaign'];
            $campaignResult = Yii::app()->db->createCommand($sql)->queryAll();

            $startTimeUser = date('H:i:s', strtotime($userOnline['starttime']));

            $campaignName = preg_replace("/ /", "\ ", $campaignResult[0]['name']);

            //se a hora que o cliente inicio o login é menor que a hora final do turno, desloguear o cliente
            if ($startTimeUser < $campaignResult[0]['daily_morning_stop_time']) {
                echo "$startTimeUser -> the operator is in the morning\n";

                if ($currentyTime > $campaignResult[0]['daily_morning_stop_time']) {
                    echo "Logout the operator\n";

                    $asmanager->Command("queue remove member SIP/" . $userOnline['id_user'] . " from " . $campaignName);

                    $totalTime = " TIME_TO_SEC( TIMEDIFF(  '" . date('Y-m-d H:i:s') . "',  starttime ) ) ";

                    $sql = "UPDATE pkg_logins_campaign SET stoptime = '" . date('Y-m-d H:i:s') . "',
						total_time = $totalTime WHERE id = " . $userOnline['id'];
                    Yii::log($sql, 'info');
                    Yii::app()->db->createCommand($sql)->execute();

                    $sql = "DELETE FROM pkg_call_online WHERE id_user = " . $resultUser[0]['id'];
                    Yii::app()->db->createCommand($sql)->execute();

                    $sql = "UPDATE pkg_user SET id_campaign = '-1' WHERE id = " . $userOnline['id_user'];
                    Yii::app()->db->createCommand($sql)->execute();

                } else {
                    echo "The operator is work in time\n";
                }

            } elseif ($startTimeUser < $campaignResult[0]['daily_afternoon_stop_time']) {
                echo "$startTimeUser -> the operator is in the afternoon\n";

                if ($currentyTime > $campaignResult[0]['daily_afternoon_stop_time']) {
                    if ($args[0] != 'localhost') {
                        $asmanager->Command("queue remove member SIP/" . $userOnline['id_user'] . " from " . $campaignName);
                    }

                    $totalTime = " TIME_TO_SEC( TIMEDIFF(  '" . date('Y-m-d H:i:s') . "',  starttime ) ) ";

                    $sql = "UPDATE pkg_logins_campaign SET stoptime = '" . date('Y-m-d H:i:s') . "',
						total_time = $totalTime WHERE id = " . $userOnline[0]['id'];
                    Yii::log($sql, 'info');
                    Yii::app()->db->createCommand($sql)->execute();

                    $sql = "UPDATE pkg_user SET id_campaign = '-1' WHERE id = " . $userOnline['id_user'];
                    Yii::app()->db->createCommand($sql)->execute();

                    $sql = "DELETE FROM pkg_call_online WHERE id_user = " . $resultUser[0]['id'];
                    Yii::app()->db->createCommand($sql)->execute();

                } else {
                    echo "The operator is work in time\n";
                }
            }
        }
    }
}
