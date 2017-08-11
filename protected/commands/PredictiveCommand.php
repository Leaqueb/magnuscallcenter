<?php
class PredictiveCommand extends ConsoleCommand
{
    public $portabilidade = false;
    private $host         = 'localhost';
    private $user         = 'magnus';
    private $password     = 'magnussolution';

    public function run($args)
    {

        include_once "/var/www/html/callcenter/protected/commands/AGI.Class.php";
        $asmanager       = new AGI_AsteriskManager;
        $conectaServidor = $conectaServidor = $asmanager->connect($this->host, $this->user, $this->password);

        /*
        menor tempo possivel de operadora ocioso.
        sem queimar numero

         */

        //mirar que campanha tiene predictive
        //mirar cuantos usuarios tiene sin llamar en esta cada una de las campañas
        //get la cantidade de numeros por cada operador sin llamar en configuraçciones
        //buscar los numeros de cada una de estas campanha con el LIMIT
        //executar las llamadas

        //tempo de pausa entre cada campanha
        $pause      = 4;
        $operadores = array();

        $log = DEBUG >= 0 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' start predictive ' . date('Y-m-d H:i:s')) : null;

        for (;;) {

            //select active campaign
            $sql            = "SELECT * FROM pkg_campaign WHERE predictive = 1 AND status = 1";
            $campaignResult = Yii::app()->db->createCommand($sql)->queryAll();

            if (count($campaignResult) == 0) {
                $msg = 'Not exists campaign with active predictive';
                $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                sleep($pause);
                continue;
            }

            $msg = "\n\n\n\nEsperar $pause ";
            $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;

            sleep($pause);

            $UNIX_TIMESTAMP = "UNIX_TIMESTAMP(";
            $tab_day        = array(1 => 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
            $name_day       = $tab_day[date('N')];

            //da um loop pela quantidade de campanha encontrada
            for ($i = 0; $i < count($campaignResult); $i++) {

                //verificar se esta dentro de uma pausa obrigatoria. Se estiver nao mandar chamada.
                $sql = "SELECT * FROM pkg_breaks WHERE '" . date('H:i:s') . "' > start_time AND
									'" . date('H:i:s') . "' < stop_time AND obrigatoria = 1";
                $pausaResult = Yii::app()->db->createCommand($sql)->queryAll();

                if (count($pausaResult) > 0) {
                    echo "Nao enviar chamada porque estamos em pausa obrigatoria";
                    sleep(1);
                    continue;
                }

                $nowtime = date('H:s');

                if ($nowtime > $campaignResult[$i]['daily_morning_start_time'] &&
                    $nowtime < $campaignResult[$i]['daily_morning_stop_time']) {
                    //echo "turno manha";
                } elseif ($nowtime > $campaignResult[$i]['daily_afternoon_start_time'] &&
                    $nowtime < $campaignResult[$i]['daily_afternoon_stop_time']) {
                    //echo "Turno Tarde";
                } else {
                    echo "sem turno agora";
                    $log = DEBUG >= 0 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ .
                        ' Campanha fora de turno' . $campaignResult[$i]['name']) : null;
                    continue;
                }

                /*
                tempo a ser esperado entre cada tentativa de envio para o operadora.
                EX: se for 30, o preditivo envia as chamadas, e se o operadora continuar livre por
                30 segundos, o sistema ira enviar mais chamada para ele.
                 */
                $sleepTime = $campaignResult[0]["call_next_try"];

                /*
                call_limit = 0, sera calculado automatico o total de chamada a ser enviado por cada operador usando $nbpage.
                call_limit > 0, subscreveta a varialvem $nbpage e sera usando como o total de chamada por cada operador
                 */

                $call_limit = $campaignResult[0]["call_limit"];

                //se call_limit > 0, nao precisa calcular o $nbpage
                if ($call_limit == 0) {
                    /*
                    total de chamadas / pelas atendidas: Ex: foi realizado 100 chamadas e atendidas 40. $nbpage sera 2.5 intval 2
                    ESta variavel $nbpage, sera usada para calcular quantas chamadas devera ser enviada para cada operadora livre.
                     */

                    //verifico o total de chamadas que foram ATENDIDAS da campanha,
                    $sql = "SELECT count(*) total  FROM pkg_preditive_gen WHERE id_phonebook IN
									(SELECT id_phonebook FROM pkg_campaign_phonebook
									WHERE id_campaign = " . $campaignResult[$i]['id'] . ") AND ringing_time > 1 ";
                    $callAnswerResult = Yii::app()->db->createCommand($sql)->queryAll();

                    //pego o total de chamadas, atendidas ou nao.
                    $sql = "SELECT count(*) total FROM pkg_preditive_gen WHERE id_phonebook IN
		        					(SELECT id_phonebook FROM pkg_campaign_phonebook WHERE id_campaign = " . $campaignResult[$i]['id'] . ")";
                    $callTotalResult = Yii::app()->db->createCommand($sql)->queryAll();

                    $nbpage = @intval($callTotalResult[0]['total'] / $callAnswerResult[0]['total']);
                }

                //calculo o tempo medio do RING que as chamadas ATENDIDAS estao demorando
                $sql           = "SELECT AVG( ringing_time ) AS AVG_ringing_time FROM  pkg_preditive_gen WHERE id_phonebook IN (SELECT id_phonebook FROM pkg_campaign_phonebook WHERE id_campaign = " . $campaignResult[$i]['id'] . ") AND ringing_time > 0 ";
                $ringingResult = Yii::app()->db->createCommand($sql)->queryAll();
                $ringing_time  = intval($ringingResult[0]['AVG_ringing_time']);

                $log = DEBUG >= 0 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ringingResult' . print_r($ringingResult, true)) : null;

                $userNotInUse = 0;
                //Inicio a verificacao do status dos operadores da campanha
                $msg    = 'queue show "' . $campaignResult[$i]['name'] . '"';
                $log    = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                $server = $asmanager->Command('queue show "' . $campaignResult[$i]['name'] . '"');
                $log    = DEBUG >= 2 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . print_r(explode("\n", $server["data"]), true)) : null;

                //$operadores contem os operadores livres e o time que foi enviado a ultima chamada.

                foreach (explode("\n", $server["data"]) as $key => $value) {
                    //Quantos operadores estao com status not in use
                    if (!preg_match("/paused/", $value) && preg_match("/Not in use/", $value)) {
                        $operador = explode(" ", substr(trim($value), 4));
                        $operador = $operador[0];
                        $s        = 0;

                        foreach ($operadores as $key => $value2) {

                            //se o operadora esta na array de operadores, entao verificamos se temos que
                            //reenviar chamadas ou nao enviar porque esta dentro do  $sleeoTime
                            if (array_key_exists($operador, $value2)) {

                                if (($value2[$operador] + $sleepTime) > time()) {
                                    $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ .
                                        " Acabamos de gerar uma chamada para operador $operador nao gerar outra: " . gmdate("Y-m-d H:i:s", $value2[$operador])) : null;
                                    continue 2;
                                } else {
                                    if (isset($operadores[$s])) {
                                        $msg = "Refazer chamada para o $operador unset(" . print_r($operadores[$s], true) . ")";
                                        $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                                        //removemos de $operadores para adicionaremos abaixo com o novo tempo.
                                        unset($operadores[$s]);
                                    }
                                }
                                $s++;
                            }

                        }

                        $msg = "Tem operador livre $operador";
                        $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                        //adicionamos em operadores com o time
                        $operadores[] = array($operador => time());
                        $userNotInUse++;

                    } else if (preg_match("/paused/", $value)) {
                        // operadores pausados
                        $operador = explode(" ", substr(trim($value), 4));
                        $operador = $operador[0];

                        $sql             = "SELECT id FROM pkg_user WHERE username = '$operador'";
                        $userPauseResult = Yii::app()->db->createCommand($sql)->queryAll();

                        $sql             = "SELECT time_start_cat, media_to_cat FROM pkg_call_online WHERE id_user = " . $userPauseResult[0]['id'];
                        $userPauseResult = Yii::app()->db->createCommand($sql)->queryAll();
                        /*
                        vamos tentar prever quando o operador ficara livre, pegando tempo medio que ele gasta para categorizar
                         */
                        if (isset($userPauseResult[0]['time_start_cat'])) {

                            $pauseTime = time() - $userPauseResult[0]['time_start_cat'];
                            //se o tempo em pausa for maior que (media pausa - media ring ) e menor que a media iniciar chamada
                            if ($pauseTime > ($userPauseResult[0]['media_to_cat'] - $ringing_time) && $pauseTime < $userPauseResult[0]['media_to_cat']) {

                                $p = 0;
                                foreach ($operadores as $key => $value3) {
                                    //mesma logica de quando o operador esta livre.
                                    if (array_key_exists($operador, $value3)) {

                                        if (($value3[$operador] + $sleepTime) > time()) {

                                            $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . " ---------->TENTAR Acabamos de gerar uma chamada para operador $operador nao gerar outra: " . gmdate("Y-m-d H:i:s", $value3[$operador])) : null;
                                            break;
                                        } else {
                                            if (isset($operadores[$p])) {
                                                $msg = "TENTAR enviar chamada para operadora   " . print_r($operador, true) . " esta em pausa a " . $pauseTime . "s e sua media de categorizacao é " . $userPauseResult[0]['media_to_cat'] . 's, e o tempo ringando é ' . $ringing_time . 's';
                                                $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                                                $msg = "TENTAR Tem operador livre $operador";
                                                $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                                                unset($operadores[$s]);
                                                $operadores[] = array($operador => time());
                                                $userNotInUse++;
                                            }
                                        }
                                        $p++;
                                    }
                                }
                            }
                        }
                    }
                    //pegamos o total de chamadas que tem na campanha
                    if (preg_match("/strategy/", $value)) {
                        $resultLimit = explode(" ", $value);
                        $totalCalls  = $resultLimit[2];
                    }
                }

                //evitamos de que se tem chamadas em espera e tem operador livre, nao geramos para evitar queimar numeros
                if ($totalCalls > $userNotInUse) {
                    $msg   = " No send call, becouse have call: total call " . $totalCalls . ', operator not in use' . $userNotInUse;
                    $log   = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                    $pause = 4;
                    continue;
                }

                if ($userNotInUse == 0) {
                    $msg   = "Not have free operador";
                    $log   = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                    $pause = 4;
                    //if no have user free, continue to next.
                    continue;
                }

                $msg = "Tem $userNotInUse operador disponivel";
                $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;

                $msg = "Tentar enviar chamadas\n";
                $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;

                $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' LOG:' . "Total de livres $userNotInUse") : null;

                if ($call_limit == 0) {
                    $nbpage = $nbpage * $userNotInUse;

                    if ($nbpage == 0) {
                        $nbpage = 3;
                    }

                    if ($nbpage > 10) {
                        //evita mandar mais que 10 chamadas por operador, mesmo se o ASR da campanha for ruin
                        $log    = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' O ASR da campanha ' . $campaignResult[$i]['name'] . " esta muito baixo") : null;
                        $nbpage = 10;
                    }
                    $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . 'LOG:' . "LIMIT automatico $nbpage ") : null;
                } else {
                    $nbpage = $call_limit * $userNotInUse;
                    $log    = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . 'LOG:' . "LIMIT manual= $nbpage ") : null;
                }

                $datebackcall = date('Y-m-d H:i', mktime(date('H'), date('i') - 10, date('s'), date('m'), date('d'), date('Y')));

                $sql = "SELECT t.id, t.number, pkg_phonebook.id_trunk, t.id_phonebook, pkg_campaign_phonebook.id_campaign, t.id_category, datebackcall
					FROM  pkg_phonenumber t
					INNER JOIN pkg_phonebook ON t.id_phonebook = pkg_phonebook.id
					INNER JOIN pkg_campaign_phonebook ON pkg_campaign_phonebook.id_phonebook = pkg_phonebook.id
					WHERE pkg_campaign_phonebook.id_campaign = " . $campaignResult[$i]['id'] . "
					AND t.id_category = 1 OR ( t.id_category = 2 AND datebackcall BETWEEN '" . $datebackcall . "' AND NOW())
					ORDER BY t.datebackcall DESC
					LIMIT 0, $nbpage";

                $callResult = Yii::app()->db->createCommand($sql)->queryAll();

                $log = DEBUG >= 5 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . $sql) : null;

                if (count($callResult) == 0) {
                    echo $sql;
                    echo 'NO PHONE FOR CALL';
                    $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . " NO PHONE FOR CALL") : null;
                    continue;
                }

                foreach ($callResult as $phone) {

                    $destination = $phone['number'];

                    $destination = Portabilidade::getDestination($destination, $phone['id_phonebook']);
                    if ($phone['number'] != $destination) {
                        //55341 5551982464731
                        $rn1                 = substr($phonenumber, 0, 5);
                        $sql                 = "SELECT * FROM pkg_trunk WHERE id IN (SELECT id_trunk FROM pkg_codigos_trunks WHERE id_codigo IN (SELECT id FROM pkg_codigos WHERE company = (SELECT company FROM pkg_codigos WHERE prefix = '$rn1')) ) ORDER BY RAND()";
                        $resultPortabilidade = Yii::app()->db->createCommand($sql)->queryAll();

                        if (count($resultPortabilidade) > 0) {
                            $phone['id_trunk'] = $resultPortabilidade[0]['id'];
                        } else {
                            $agi->verbose('Portabilidade ativa, mas sem tronco para ' . $rn1, 3);
                        }
                    }

                    $log = DEBUG >= 4 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . " DESTINATION " . $destination) : null;

                    $sql          = "SELECT * FROM pkg_trunk WHERE id = '" . $phone['id_trunk'] . "' ";
                    $resultTrunk  = Yii::app()->db->createCommand($sql)->queryAll();
                    $idTrunk      = $resultTrunk[0]['id'];
                    $trunkcode    = $resultTrunk[0]['trunkcode'];
                    $trunkprefix  = $resultTrunk[0]['trunkprefix'];
                    $removeprefix = $resultTrunk[0]['removeprefix'];
                    $providertech = $resultTrunk[0]['providertech'];

                    $extension = $destination;
                    //retiro e adiciono os prefixos do tronco
                    if (strncmp($destination, $removeprefix, strlen($removeprefix)) == 0) {
                        $destination = substr($destination, strlen($removeprefix));
                    }

                    $destination = $trunkprefix . $destination;

                    $extension = $destination;

                    $dialstr = "$providertech/$trunkcode/$destination";
                    //$dialstr = "$providertech/$trunkcode/45".rand(1,2).rand(0,9);

                    $aleatorio = str_replace(" ", "", microtime(true));

                    // gerar os arquivos .call
                    $call = "Channel: " . $dialstr . "\n";
                    $call .= "MaxRetries: 0\n";
                    $call .= "RetryTime: 1\n";
                    $call .= "WaitTime: 45\n";
                    $call .= "Context: billing\n";
                    $call .= "Extension: " . $extension . "\n";
                    $call .= "Priority: 1\n";
                    $call .= "Set:CALLERID=" . $phone['number'] . "\n";
                    $call .= "Set:CALLED=" . $extension . "\n";
                    $call .= "Set:PHONENUMBER_ID=" . $phone['id'] . "\n";
                    $call .= "Set:IDPHONEBOOK=" . $phone['id_phonebook'] . "\n";
                    $call .= "Set:CAMPAIGN_ID=" . $phone['id_campaign'] . "\n";
                    $call .= "Set:IDTRUNK=" . $phone['id_trunk'] . "\n";
                    $call .= "Set:STARTCALL=" . time() . "\n";
                    $call .= "Set:ALEARORIO=" . $aleatorio . "\n";

                    $log = DEBUG >= 4 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . $call) : null;

                    $msg = "Enviado chamada para  $extension";
                    echo 'LOG:' . $msg . "\n";
                    $log = DEBUG >= 1 ? MagnusLog::writeMagnusLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;

                    $arquivo_call = "/tmp/$aleatorio.call";

                    $fp = fopen("$arquivo_call", "a+");
                    fwrite($fp, $call);
                    fclose($fp);

                    touch("$arquivo_call", mktime(date("H"), date("i"), date("s") + 1, date("m"), date("d"), date("Y")));
                    chown("$arquivo_call", "asterisk");
                    chgrp("$arquivo_call", "asterisk");
                    chmod("$arquivo_call", 0755);
                    exec("mv $arquivo_call /var/spool/asterisk/outgoing/$aleatorio.call");

                    //system("cat /var/spool/asterisk/outgoing/$aleatorio.call");

                    //desativamos o numero para nao ser usado novamente.
                    $sql = "UPDATE pkg_phonenumber SET id_category = 0 WHERE id = " . $phone['id'];
                    Yii::app()->db->createCommand($sql)->execute();

                    //salvamos os dados da chamada gerada
                    $sql = "INSERT INTO pkg_preditive_gen (date, uniqueID,id_phonebook) VALUES ('" . time() . "', " . $aleatorio . ", " . $phone['id_phonebook'] . ")";
                    Yii::app()->db->createCommand($sql)->execute();
                }
            }

        }

    }
}
