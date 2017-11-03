<?php
class PredictiveCommand extends ConsoleCommand
{
    public $portabilidade = false;

    public function run($args)
    {

        $this->debug = 10;

        //$channel = AsteriskAccess::getCoreShowChannel($_POST['channel']);
        /*
        Objetivo:
        menor tempo possivel de operadora ocioso.
        sem queimar numero

        Pegar as campanhas com predictive ativo
        Verificar quantos operadores tem na campanha com o status FREE
        Pegar a quantidade de numeros a ser enviado por operadora
        buscar os números de cada uma das campanha com o total de numeros no LIMIT
        executar las llamadas

         */

        //Tempo de pausa entre cada campanha
        $pause      = 4;
        $operadores = array();

        $log = $this->debug >= 0 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' start predictive ' . date('Y-m-d H:i:s')) : null;

        for (;;) {

            //select active campaign
            $modelCampaign = Campaign::model()->findAll('predictive = 1 AND status = 1');

            if (!count($modelCampaign)) {
                $msg = 'Not exists campaign with active predictive';
                $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                sleep($pause);
                continue;
            }

            $msg = "\n\n\n\nEsperar $pause ";
            $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;

            sleep($pause);

            //da um loop pela quantidade de campanha encontrada
            for ($i = 0; $i < count($modelCampaign); $i++) {
                $time = date('H:i:s');

                //verificar se esta dentro de uma pausa obrigatoria. Se estiver nao mandar chamada.
                $modelBreaks = Breaks::model()->find('mandatory = 1 AND :key > start_time AND :key < stop_time', array(':key' => $time));

                if (count($modelBreaks)) {
                    echo "Nao enviar chamada porque estamos em pausa obrigatoria";
                    sleep(1);
                    continue;
                }

                $nowtime = date('H:s');

                if ($nowtime > $modelCampaign[$i]->daily_morning_start_time &&
                    $nowtime < $modelCampaign[$i]->daily_morning_stop_time) {
                    //echo "turno manha";
                } elseif ($nowtime > $modelCampaign[$i]->daily_afternoon_start_time &&
                    $nowtime < $modelCampaign[$i]->daily_afternoon_stop_time) {
                    //echo "Turno Tarde";
                } else {
                    echo "sem turno agora";
                    $log = $this->debug >= 0 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ .
                        ' Campanha fora de turno' . $modelCampaign[$i]['name']) : null;
                    continue;
                }

                /*
                tempo a ser esperado entre cada tentativa de envio para o operadora.
                EX: se for 30, o preditivo envia as chamadas, e se o operadora continuar livre por
                30 segundos, o sistema ira enviar mais chamada para ele.
                 */
                $sleepTime = $modelCampaign[$i]->call_next_try;

                /*
                call_limit = 0, sera calculado automatico o total de chamada a ser enviado por cada operador usando $nbpage.
                call_limit > 0, subscreveta a varialvem $nbpage e sera usando como o total de chamada por cada operador
                 */

                $call_limit = $modelCampaign[$i]->call_limit;

                //se call_limit > 0, nao precisa calcular o $nbpage
                if ($call_limit == 0) {
                    /*
                    Total de chamadas / pelas atendidas: Ex: foi realizado 100 chamadas e atendidas 40. $nbpage sera 2.5 intval 2
                    Esta variavel $nbpage, sera usada para calcular quantas chamadas devera ser enviada para cada operadora livre.
                     */

                    //verifico o total de chamadas que foram ATENDIDAS da campanha,
                    $criteria            = new CDbCriteria();
                    $criteria->condition = 'ringing_time > 1 AND id_phonebook IN (SELECT id_phonebook FROM pkg_campaign_phonebook  WHERE id_campaign = :key) ';
                    $criteria->params    = array(':key' => $modelCampaign[$i]->id);
                    $totalAnswerdCalls   = PredictiveGen::model()->count($criteria);

                    //pego o total de chamadas, atendidas ou nao.
                    $criteria            = new CDbCriteria();
                    $criteria->condition = 'id_phonebook IN (SELECT id_phonebook FROM pkg_campaign_phonebook  WHERE id_campaign = :key) ';
                    $criteria->params    = array(':key' => $modelCampaign[$i]->id);
                    $totalCalls          = PredictiveGen::model()->count($criteria);

                    $nbpage = @intval($totalCalls / $totalAnswerdCalls);
                }

                //calculo o tempo medio do RING que as chamadas ATENDIDAS estao demorando
                $criteria            = new CDbCriteria();
                $criteria->select    = 'AVG( ringing_time ) AS AVG_ringing_time';
                $criteria->condition = 'ringing_time > 1 AND id_phonebook IN (SELECT id_phonebook FROM pkg_campaign_phonebook  WHERE id_campaign = :key) ';
                $criteria->params    = array(':key' => $modelCampaign[$i]->id);
                $averageRingingTime  = PredictiveGen::model()->findAll($criteria);
                $averageRingingTime  = intval($averageRingingTime);

                $userNotInUse = 0;
                //Inicio a verificacao do status dos operadores da campanha
                $server = AsteriskAccess::instance()->queueShow($modelCampaign[$i]->name);

                $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . 'queue show ' . $modelCampaign[$i]->name) : null;

                $log = $this->debug >= 2 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . print_r(explode("\n", $server['data']), true)) : null;

                //$operadores contem os operadores livres e o time que foi enviado a ultima chamada.

                foreach (explode("\n", $server["data"]) as $key => $value) {

                    //Quantos operadores estao com status not in use
                    if (!preg_match("/paused/", $value) && preg_match("/Not in use/", $value)) {

                        $operador = explode(" ", substr(trim($value), 4));
                        $operador = $operador[0];

                        $s = 0;

                        foreach ($operadores as $key => $value2) {

                            //se o operadora esta na array de operadores, entao verificamos se temos que
                            //reenviar chamadas ou nao enviar porque esta dentro do  $sleeoTime
                            if (array_key_exists($operador, $value2)) {

                                if (($value2[$operador] + $sleepTime) > time()) {
                                    $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ .
                                        " Acabamos de gerar uma chamada para operador $operador nao gerar outra: " . gmdate("Y-m-d H:i:s", $value2[$operador])) : null;
                                    continue 2;
                                } else {
                                    if (isset($operadores[$s])) {
                                        $msg = "Refazer chamada para o $operador unset(" . print_r($operadores[$s], true) . ")";
                                        $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                                        //removemos de $operadores para adicionaremos abaixo com o novo tempo.
                                        unset($operadores[$s]);
                                    }
                                }
                                $s++;
                            }

                        }

                        $msg = "Tem operador livre $operador";
                        $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                        //adicionamos em operadores com o time
                        $operadores[] = array($operador => time());
                        $userNotInUse++;

                    } else if (preg_match("/paused/", $value)) {
                        // operadores pausados
                        $operador = explode(" ", substr(trim($value), 4));
                        $operador = $operador[0];

                        $modelCallOnline = CallOnline::model()->findAll(array(
                            'condition' => $this->filter,
                            'params'    => array(':key' => $operador),
                            'with'      => array(
                                'idUser' => array(
                                    'condition' => "idUser.username = :key",
                                ),
                            ),
                        ));

                        /*
                        vamos tentar prever quando o operador ficara livre, pegando tempo medio que ele gasta para categorizar
                         */
                        if (count($modelCallOnline)) {

                            $pauseTime = time() - $modelCallOnline[0]->time_start_cat;
                            //se o tempo em pausa for maior que (media pausa - media ring ) e menor que a media iniciar chamada
                            if ($pauseTime > ($modelCallOnline[0]->media_to_cat - $averageRingingTime) && $pauseTime < $modelCallOnline[0]->media_to_cat) {

                                $p = 0;
                                foreach ($operadores as $key => $value3) {
                                    //mesma logica de quando o operador esta livre.
                                    if (array_key_exists($operador, $value3)) {

                                        if (($value3[$operador] + $sleepTime) > time()) {

                                            $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . " ---------->TENTAR Acabamos de gerar uma chamada para operador $operador nao gerar outra: " . gmdate("Y-m-d H:i:s", $value3[$operador])) : null;
                                            break;
                                        } else {
                                            if (isset($operadores[$p])) {
                                                $msg = "TENTAR enviar chamada para operadora   " . print_r($operador, true) . " esta em pausa a " . $pauseTime . "s e sua media de categorizacao é " . $modelCallOnline[0]->media_to_cat . 's, e o tempo ringando é ' . $averageRingingTime . 's';
                                                $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                                                $msg = "TENTAR Tem operador livre $operador";
                                                $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
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
                    $log   = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                    $pause = 4;
                    continue;
                }

                if ($userNotInUse == 0) {
                    $msg   = "Not have free operador";
                    $log   = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;
                    $pause = 4;
                    //if no have user free, continue to next.
                    continue;
                }

                $msg = "Tem $userNotInUse operador disponivel";
                $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;

                $msg = "Tentar enviar chamadas\n";
                $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;

                if ($call_limit == 0) {
                    $nbpage = $nbpage * $userNotInUse;

                    if ($nbpage == 0) {
                        $nbpage = 3;
                    }

                    if ($nbpage > 10) {
                        //evita mandar mais que 10 chamadas por operador, mesmo se o ASR da campanha for ruin
                        $log    = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' O ASR da campanha ' . $modelCampaign[$i]->name . " esta muito baixo") : null;
                        $nbpage = 10;
                    }
                    $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . 'LOG:' . "LIMIT automatico $nbpage ") : null;
                } else {
                    $nbpage = $call_limit * $userNotInUse;
                    $log    = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . 'LOG:' . "LIMIT manual= $nbpage ") : null;
                }

                //get all campaign phonebook
                $modelCampaignPhonebook = CampaignPhonebook::model()->findAll('id_campaign = :key', array(':key' => $modelCampaign[$i]->id));
                $ids_phone_books        = array();
                foreach ($modelCampaignPhonebook as $key => $phonebook) {
                    $ids_phone_books[] = $phonebook->id_phonebook;
                }
                $datebackcall = date('Y-m-d H:i', mktime(date('H'), date('i') - 10, date('s'), date('m'), date('d'), date('Y')));

                $criteria = new CDbCriteria();
                $criteria->addCondition('id_phonebook IN ( SELECT id_phonebook FROM pkg_campaign_phonebook WHERE id_campaign = :key1 ) AND id_category = 1 OR ( id_category = 2 AND datebackcall BETWEEN :key AND NOW())');
                $criteria->params[':key']  = $datebackcall;
                $criteria->params[':key1'] = $modelCampaign[$i]->id;
                $criteria->order           = 'datebackcall DESC';
                $criteria->limit           = $nbpage;
                $modelPhoneNumber          = PhoneNumber::model()->findAll($criteria);

                if (!count($modelPhoneNumber)) {
                    echo $sql;
                    echo 'NO PHONE FOR CALL';
                    $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . " NO PHONE FOR CALL") : null;
                    continue;
                }
                $ids = array();
                foreach ($modelPhoneNumber as $phone) {

                    $types = array(
                        '0' => 'number',
                        '1' => 'number_home',
                        '2' => 'number_office',
                        '3' => 'mobile',
                    );

                    $destination = $phone->{$types[$phone->try]};

                    if ($types[$phone['try']] = !'number') {
                        echo $phone->id . ", tentando ligar para outro numero ->  " . $types[$phone->try] . " " . $phone->{$types[$phone->try]} . " \n";
                    }

                    $destination = Portabilidade::getDestination($destination, $phone->id_phonebook);
                    if ($phone->number != $destination) {
                        //55341 5551982464731
                        $rn1      = substr($phonenumber, 0, 5);
                        $criteria = new CDbCriteria();
                        $criteria->addCondition('id IN ( SELECT id_trunk FROM pkg_codigos_trunks WHERE id_codigo IN (SELECT id FROM pkg_codigos WHERE company = (SELECT company FROM pkg_codigos WHERE prefix = :key)) )');
                        $criteria->params[':key'] = $rn1;
                        $criteria->order          = 'RAND()';
                        $criteria->limit          = $nbpage;
                        $modelTrunkPortabilidade  = Trunk::model()->find($criteria);

                        if (count($modelTrunkPortabilidade)) {
                            $phone->idPhonebook->id_trunk = $modelTrunkPortabilidade->id;
                        } else {
                            $agi->verbose('Portabilidade ativa, mas sem tronco para ' . $rn1, 3);
                        }
                    }

                    $log = $this->debug >= 4 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . " DESTINATION " . $destination) : null;

                    $modelTrunk   = Trunk::model()->findByPk((int) $phone->idPhonebook->id_trunk);
                    $idTrunk      = $modelTrunk->id;
                    $trunkcode    = $modelTrunk->trunkcode;
                    $trunkprefix  = $modelTrunk->trunkprefix;
                    $removeprefix = $modelTrunk->removeprefix;
                    $providertech = $modelTrunk->providertech;

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
                    $call .= "Context: magnuscallcenter\n";
                    $call .= "Extension: " . $extension . "\n";
                    $call .= "Priority: 1\n";
                    $call .= "Set:CALLERID=" . $phone->number . "\n";
                    $call .= "Set:CALLED=" . $extension . "\n";
                    $call .= "Set:PHONENUMBER_ID=" . $phone->id . "\n";
                    $call .= "Set:IDPHONEBOOK=" . $phone->id_phonebook . "\n";
                    $call .= "Set:CAMPAIGN_ID=" . $modelCampaign[$i]->id . "\n";
                    $call .= "Set:IDTRUNK=" . $phone->idPhonebook->id_trunk . "\n";
                    $call .= "Set:STARTCALL=" . time() . "\n";
                    $call .= "Set:ALEARORIO=" . $aleatorio . "\n";

                    $log = $this->debug >= 4 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . $call) : null;

                    $msg = "Enviado chamada para  $extension";
                    echo 'LOG:' . $msg . "\n";
                    $log = $this->debug >= 1 ? MagnusLog::writeLog(LOGFILE, ' line:' . __LINE__ . ' ' . $msg) : null;

                    $arquivo_call = "/tmp/$aleatorio.call";

                    $fp = fopen("$arquivo_call", "a+");
                    fwrite($fp, $call);
                    fclose($fp);

                    $time += time();
                    touch("$arquivo_call", $time);
                    chown("$arquivo_call", "asterisk");
                    chgrp("$arquivo_call", "asterisk");
                    chmod("$arquivo_call", 0755);
                    LinuxAccess::system("mv $arquivo_call /var/spool/asterisk/outgoing/$aleatorio.call");

                    $ids[] = $phone->id;
                }

                //desativamos o numero para nao ser usado novamente.
                $criteria = new CDbCriteria();
                $criteria->addInCondition('id', $ids);
                PhoneNumber::model()->updateAll(array('id_category' => 0), $criteria);

                //salvamos os dados da chamada gerada
                $modelPredictiveGen               = new PredictiveGen();
                $modelPredictiveGen->date         = time();
                $modelPredictiveGen->uniqueID     = $aleatorio;
                $modelPredictiveGen->id_phonebook = $phone->id_phonebook;
                $modelPredictiveGen->save();
            }
        }
    }
}
