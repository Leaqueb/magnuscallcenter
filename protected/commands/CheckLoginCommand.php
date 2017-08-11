<?php
class CheckLoginCommand extends ConsoleCommand
{
    private $host     = 'localhost';
    private $user     = 'magnus';
    private $password = 'magnussolution';

    public function run($args)
    {

        $day = date('Y-m-d');

        LoginsCampaign::model()->updateAll(array('stoptime' => 'starttime'), 'stoptime = :key AND type < :key',
            array(':key' => '0000-00-00 00:00:00', ':key1' => 'login'));

        $modelCampaign = Campaign::model()->findAll(condition, array('key' => value))

        foreach ($modelCampaign as $key => $campaign) {
            $server = AsteriskAccess::instance()->queueShow($campaign->name);

            $arr = explode("\n", $server["data"]);

            if (count($arr) < 7) {
                continue;
            }

            foreach ($arr as $key => $line) {

                $line = trim($line);

                if (substr($line, 0, 3) == 'SIP') {

                    $data     = explode('(', $line);
                    $username = explode("/", $data[0]);
                    $username = trim($username[1]);

                    $modelUser = User::model()->find('username = :key', array(':key' => $username));

                    $modelLoginsCampaign = LoginsCampaign::model()->findAll('stoptime = :key AND starttime > :key1 AND type = :key2 AND id_user = :key3',
                        array(
                            ':key'  => '0000-00-00 00:00:00',
                            ':key1' => date('Y-m-d'),
                            ':key2' => 'login',
                            ':key3' => $modelUser->id,
                        ));

                    if (!count($modelLoginsCampaign)) {

                        AsteriskAccess::instance()->queueRemoveMember($data[0], $campaign->name);

                        $totalTime = " TIME_TO_SEC( TIMEDIFF(  '" . date('Y-m-d H:i:s') . "',  starttime ) ) ";

                        LoginsCampaign::model()->updateAll(
                            array(
                                'stoptime'   => date('Y-m-d H:i:s'),
                                'total_time' => $totalTime,
                            ),
                            'id = :key',
                            array(':key' => $modelLoginsCampaign->id));

                        $modelUser->id_campaign = -1;
                        $modelUser->save();

                        CallOnline::model()->deleteAll('id_user = :key', array(':key' => $modelUser->id));
                    }
                }
            }
        }

    }
}
