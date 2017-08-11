<?php
/**
 * Classe de com funcionalidades globais
 *
 * MagnusBilling <info@magnusbilling.com>
 * 08/06/2013
 */

class AsteriskAccess
{

    private $asmanager;
    private static $instance;

    public static function instance($host = 'localhost', $user = 'magnus', $pass = 'magnussolution')
    {
        if (is_null(self::$instance)) {
            self::$instance = new AsteriskAccess();
        }
        self::$instance->connectAsterisk($host, $user, $pass);
        return self::$instance;
    }

    private function __construct()
    {
        $this->asmanager = new AGI_AsteriskManager;
    }

    private function connectAsterisk($host, $user, $pass)
    {
        $this->asmanager->connect($host, $user, $pass);
    }

    public function queueAddMember($member, $queue)
    {
        $this->asmanager->Command("queue add member SIP/" . $member . " to " . preg_replace("/ /", "\ ", $queue));
    }

    public function queueRemoveMember($member, $queue)
    {
        $this->asmanager->Command("queue remove member SIP/" . $member . " from " . preg_replace("/ /", "\ ", $queue));
    }

    public function queuePauseMember($member, $queue, $reason = 'normal')
    {
        $this->asmanager->Command("queue pause member SIP/" . $member . " queue " . preg_replace("/ /", "\ ", $queue) . " reason " . $reason);
    }

    public function queueUnPauseMember($member, $queue, $reason = 'normal')
    {
        $this->asmanager->Command("queue unpause member SIP/" . $member . " queue " . preg_replace("/ /", "\ ", $queue) . " reason " . $reason);
    }

    public function queueShow($queue)
    {
        return $this->asmanager->Command("queue show " . $queue);
    }

    public function queueReload()
    {
        return $this->asmanager->Command("queue reload all");
    }

    public function queueReseteStats($queue)
    {
        return $this->asmanager->Command("queue reset stats " . $queue);
    }

    public function hangupRequest($channel)
    {
        return $this->asmanager->Command("hangup request " . $channel);
    }

    public function sipReload()
    {
        return $this->asmanager->Command("sip reload");
    }

    public function sipShowPeers()
    {
        return $this->asmanager->Command("sip show peers");
    }

    public function sipShowRegistry()
    {
        return $this->asmanager->Command("sip show registry");
    }

    public function iaxReload()
    {
        return @$this->asmanager->Command("iax reload");
    }

    public function coreShowChannelsConcise()
    {
        return @$this->asmanager->Command("core show channels concise");
    }

    public function coreShowChannel($channel)
    {
        return @$this->asmanager->Command("core show channel " . $channel);
    }
    public function sipShowChannel($channel)
    {
        return @$this->asmanager->Command("sip show channel " . $channel);
    }

    public function queueGetMemberStatus($member, $campaign_name)
    {
        $queueData = AsteriskAccess::instance()->queueShow($campaign_name);
        $queueData = explode("\n", $queueData["data"]);
        $status    = "error";
        foreach ($queueData as $key => $data) {

            $data = trim($data);

            if (preg_match("/SIP\/" . Yii::app()->session['username'] . "/", $data)) {
                $line   = explode('(', $data);
                $status = trim($line[3]);
                $status = explode(")", $status);

                $status = $status[0];
                break;
            }
        }
        return $status;
    }
    //model , file, e o nome para o contexto
    public function writeAsteriskFile($model, $file, $head_field = 'name')
    {
        $rows = Util::getColumnsFromModel($model);

        $fd = fopen($file, "w");

        LinuxAccess::exec('touch ' . $file);

        if ($head_field == 'trunkcode' && preg_match("/sip/", $file)) {
            $registerFile = '/etc/asterisk/sip_magnus_register.conf';
            LinuxAccess::exec('touch ' . $registerFile);
            $fr = fopen($registerFile, "w");
        } elseif ($head_field == 'trunkcode' && preg_match("/iax/", $file)) {
            $registerFile = '/etc/asterisk/iax_magnus_register.conf';
            LinuxAccess::exec('touch ' . $registerFile);
            $fr = fopen($registerFile, "w");
        }

        if (!$fd) {
            echo "</br><center><b><font color=red>" . gettext("Could not open buddy file") . $file . "</font></b></center>";
        } else {
            foreach ($rows as $key => $data) {
                $line         = "\n\n[" . $data[$head_field] . "]\n";
                $registerLine = '';
                foreach ($data as $key => $option) {
                    if ($key == $head_field) {
                        continue;
                    }

                    //registrar tronco
                    if ($key == 'register_string' && preg_match("/^.{3}.*:.{3}.*@.{5}.*\/.{3}.*/", $data['register_string'])) {
                        $registerLine .= 'register=>' . $data['register_string'] . "\n";
                    } elseif ($key == 'encryption' && $option == 'no') {
                        continue;
                    } elseif ($key == 'transport' && $option == 'no') {
                        continue;
                    } elseif ($key == 'port' && $option == '5060') {
                        continue;
                    } elseif ($key == 'user') {
                        $line .= $key . '=' . $option . "\n";
                        $line .= 'username=' . $option . "\n";
                    } else {
                        $line .= $key . '=' . $option . "\n";
                    }
                }

                if (fwrite($fr, $registerLine) === false) {
                    echo gettext("Impossible to write to the file") . " ($registerLine)";
                    break;
                }

                if (fwrite($fd, $line) === false) {
                    echo "Impossible to write to the file ($buddyfile)";
                    break;
                }
            }

            fclose($fd);

            if (preg_match("/sip/", $file)) {
                AsteriskAccess::instance()->sipReload();
            } elseif (preg_match("/iax/", $file)) {
                AsteriskAccess::instance()->iaxReload();
            } else {
                AsteriskAccess::instance()->queueReload();
            }

        }
    }
    //call file , time in seconds to create the file
    public static function generateCallFile($callFile, $time = 0)
    {
        $aleatorio    = str_replace(" ", "", microtime(true));
        $arquivo_call = "/tmp/$aleatorio.call";
        $fp           = fopen("$arquivo_call", "a+");
        fwrite($fp, $callFile);
        fclose($fp);

        $time += time();

        touch("$arquivo_call", $time);
        @chown("$arquivo_call", "asterisk");
        @chgrp("$arquivo_call", "asterisk");
        chmod("$arquivo_call", 0755);

        LinuxAccess::system("mv $arquivo_call /var/spool/asterisk/outgoing/$aleatorio.call");
    }

    public function getCallsPerUser($accountcode)
    {
        $channelsData = AsteriskAccess::instance()->coreShowChannelsConcise();
        $channelsData = explode("\n", $channelsData["data"]);
        $modelSip     = Sip::model()->findAll('accountcode = :key', array(':key' => $accountcode));
        $sipAccounts  = '';
        foreach ($modelSip as $key => $sip) {
            $sipAccounts .= $sip->name . '|';
        }

        $sipAccounts = substr($sipAccounts, 0, -1);
        $calls       = 0;
        foreach ($channelsData as $key => $line) {
            if (preg_match("/^SIP\/($sipAccounts)-/", $line)) {
                $calls++;
            }
        }

        return $calls;
    }

    public function groupTrunk($agi, $ipaddress, $maxuse)
    {
        if ($maxuse > 0) {

            $agi->verbose('Trunk have channels limit', 8);
            //Set group to count the trunk call use
            $agi->set_variable("GROUP()", $ipaddress);

            $groupData = @$this->asmanager->Command("group show channels");

            $arr   = explode("\n", $groupData["data"]);
            $count = 0;
            if ($arr[0] != "") {
                foreach ($arr as $temp) {
                    $linha = explode("  ", $temp);

                    if (trim($linha[4]) == $ipaddress) {
                        $channel = @$this->asmanager->Command("core show channel " . $linha[0]);
                        $arr2    = explode("\n", $channel["data"]);

                        foreach ($arr2 as $temp2) {
                            if (strstr($temp2, 'State:')) {
                                $arr3   = explode("State:", $temp2);
                                $status = trim(rtrim($arr3[1]));
                            }
                        }

                        if (preg_match("/Up |Ring /", $status)) {
                            $count++;
                        }
                    }
                }
            }
            if ($count > $maxuse) {
                $agi->verbose('Trunk ' . $ipaddress . ' have  ' . $count . ' calls, and the maximun call is ' . $maxuse, 2);
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public static function getSipShowPeers()
    {
        $modelServers = Servers::model()->getAllAsteriskServers();
        $result       = array();
        foreach ($modelServers as $key => $server) {
            $data = AsteriskAccess::instance($server['host'], $server['username'], $server['password'])->sipShowPeers();

            if (!isset($data['data']) || strlen($data['data']) < 10) {
                continue;
            }

            $linesSipResult = explode("\n", $data['data']);

            $column  = 'Name/username             Host                                    Dyn Forcerport Comedia    ACL Port     Status      Description';
            $columns = preg_split("/\s+/", $column);

            $index = array();

            for ($i = 0; $i < 10; $i++) {
                $index[] = @strpos($column, $columns[$i]);
            }

            foreach ($linesSipResult as $key => $line) {
                $element = array();
                foreach ($index as $key => $value) {
                    $startIndex               = $value;
                    $lenght                   = @$index[$key + 1] - $value;
                    @$element[$columns[$key]] = trim(isset($index[$key + 1]) ? substr($line, $startIndex, $lenght) : substr($line, $startIndex));
                }
                $result[] = $element;

            }
        }
        return $result;
    }

    public static function getCoreShowChannels()
    {

        $modelServers = Servers::model()->getAllAsteriskServers();
        $channels     = array();
        foreach ($modelServers as $key => $server) {

            $columns = array('Channel', 'Context', 'Exten', 'Priority', 'Stats', 'Application', 'Data', 'CallerID', 'Accountcode', 'Amaflags', 'Duration', 'Bridged');
            $data    = AsteriskAccess::instance($server['host'], $server['username'], $server['password'])->coreShowChannelsConcise();

            if (!isset($data) || !isset($data['data'])) {
                return;
            }

            $linesCallsResult = explode("\n", $data['data']);

            if (count($linesCallsResult) < 1) {
                return;
            }

            for ($i = 0; $i < count($linesCallsResult); $i++) {
                $call = explode("!", $linesCallsResult[$i]);
                if (!preg_match("/\//", $call[0])) {
                    continue;
                }
                $call['server'] = $server['host'];
                $channels[]     = $call;

            }

        }
        return $channels;
    }
    public function getCoreShowChannel($channel)
    {

        $modelServers = Servers::model()->getAllAsteriskServers();
        $channels     = array();
        foreach ($modelServers as $key => $server) {

            $data = AsteriskAccess::instance($server['host'], $server['username'], $server['password'])->coreShowChannel($channel);
            if (!isset($data['data']) || strlen($data['data']) < 10 || preg_match("/is not a known channe/", $data['data'])) {
                continue;
            }
            $linesCallResult = explode("\n", $data['data']);
            if (count($linesCallResult) < 1) {
                continue;
            }
            $result = array();
            for ($i = 2; $i < count($linesCallResult); $i++) {
                if (preg_match("/level 1: /", $linesCallResult[$i])) {
                    $data = explode("=", substr($linesCallResult[$i], 9));
                } elseif (preg_match("/: /", $linesCallResult[$i])) {
                    $data = explode(":", $linesCallResult[$i]);
                } elseif (preg_match("/=/", $linesCallResult[$i])) {
                    $data = explode("=", $linesCallResult[$i]);
                }
                // echo '<pre>';
                //print_r($data);
                $key   = isset($data[0]) ? $data[0] : '';
                $value = isset($data[1]) ? $data[1] : '';

                if ($key == 'SIPCALLID') {
                    $result[trim($key)] = AsteriskAccess::instance($server['host'], $server['username'], $server['password'])->sipShowChannel(trim($value));

                } else {
                    $result[trim($key)] = trim($value);
                }

            }
            break;
        }

        return $result;

    }

    public function generateSipPeers()
    {

        $select = 'id, accountcode, name, defaultuser, secret, regexten, amaflags, callerid, language, cid_number, disallow, allow, directmedia, context, dtmfmode, insecure, nat, qualify, type, host, calllimit'; // add

        $list_friend = Sip::model()->findAll();

        $buddyfile = '/etc/asterisk/sip_magnus_user.conf';

        if (is_array($list_friend)) {

            $fd = fopen($buddyfile, "w");

            if ($fd) {
                foreach ($list_friend as $key => $data) {
                    $line = "\n\n[" . $data['name'] . "]\n";
                    if (fwrite($fd, $line) === false) {
                        echo "Impossible to write to the file ($buddyfile)";
                        break;
                    } else {
                        $line = '';

                        $line .= 'host=' . $data['host'] . "\n";

                        $line .= 'fromdomain=' . $data['host'] . "\n";
                        $line .= 'accountcode=' . $data['accountcode'] . "\n";
                        $line .= 'disallow=' . $data['disallow'] . "\n";

                        $codecs = explode(",", $data['allow']);
                        foreach ($codecs as $codec) {
                            $line .= 'allow=' . $codec . "\n";
                        }

                        if (strlen($data['directmedia']) > 1) {
                            $line .= 'directmedia=' . $data['directmedia'] . "\n";
                        }

                        if (strlen($data['context']) > 1) {
                            $line .= 'context=' . $data['context'] . "\n";
                        }

                        if (strlen($data['dtmfmode']) > 1) {
                            $line .= 'dtmfmode=' . $data['dtmfmode'] . "\n";
                        }

                        if (strlen($data['insecure']) > 1) {
                            $line .= 'insecure=' . $data['insecure'] . "\n";
                        }

                        if (strlen($data['nat']) > 1) {
                            $line .= 'nat=' . $data['nat'] . "\n";
                        }

                        if (strlen($data['qualify']) > 1) {
                            $line .= 'qualify=' . $data['qualify'] . "\n";
                        }

                        if (strlen($data['type']) > 1) {
                            $line .= 'type=' . $data['type'] . "\n";
                        }

                        if (strlen($data['regexten']) > 1) {
                            $line .= 'regexten=' . $data['regexten'] . "\n";
                        }

                        if (strlen($data['amaflags']) > 1) {
                            $line .= 'amaflags=' . $data['amaflags'] . "\n";
                        }

                        if (strlen($data['cid_number']) > 1) {
                            $line .= 'cid_number=' . $data['cid_number'] . "\n";
                        }

                        if (strlen($data['language']) > 1) {
                            $line .= 'language=' . $data['language'] . "\n";
                        }

                        if (strlen($data['defaultuser']) > 1) {
                            $line .= 'defaultuser=' . $data['defaultuser'] . "\n";
                        }

                        if (strlen($data['fromuser']) > 1) {
                            $line .= 'fromuser=' . $data['fromuser'] . "\n";
                        }

                        if (strlen($data['secret']) > 1) {
                            $line .= 'secret=' . $data['secret'] . "\n";
                        }

                        if ($data['calllimit'] > 0) {
                            $line .= 'call-limit=' . $data['calllimit'] . "\n";
                        }

                        if ($data['context'] == 'encryption') {
                            $line .= "encryption=yes\n";
                            $line .= "avpf=yes\n";
                            $line .= "force_avp=yes\n";
                            $line .= "icesupport=yes\n";
                            $line .= "dtlsenable=yes\n";
                            $line .= "dtlsverify=fingerprint\n";
                            $line .= "dtlscertfile=/etc/asterisk/certificate/asterisk.pem\n";
                            $line .= "dtlscafile=/etc/asterisk/certificate/ca.crt\n";
                            $line .= "dtlssetup=actpass\n";
                            $line .= "rtcp_mux=yes\n";
                        }

                        if (fwrite($fd, $line) === false) {
                            echo gettext("Impossible to write to the file") . " ($buddyfile)";
                            break;
                        }
                    }
                }

                fclose($fd);
            }

        }

        $list_friend = Sip::model()->findAll(array(
            'select' => $select,
        ));
        $subscriberfile = '/etc/asterisk/sip_magnus_subscriber.conf';
        $subscriber     = '[subscribe]';
        if (is_array($list_friend)) {
            $fsubs = fopen($subscriberfile, "w");
            foreach ($list_friend as $key => $data) {
                if (strlen($data['defaultuser']) > 1) {
                    $subscriber .= 'exten => ' . $data['defaultuser'] . ',hint,SIP/' . $data['defaultuser'] . "\n";
                }

            }
            fwrite($fsubs, $subscriber);
            fclose($fsubs);
        }

        AsteriskAccess::instance()->sipReload();

    }
    public function generateIaxPeers()
    {

        $select = 'id, accountcode, name, defaultuser, secret, regexten, amaflags, callerid, language, cid_number, disallow, allow, directmedia, context, dtmfmode, insecure, nat, qualify, type, host, calllimit'; // add

        $list_friend = Iax::model()->findAll();

        $buddyfile = '/etc/asterisk/iax_magnus_user.conf';

        Yii::log($buddyfile, 'error');

        if (is_array($list_friend)) {

            $fd = fopen($buddyfile, "w");

            if ($fd) {
                foreach ($list_friend as $key => $data) {
                    $line = "\n\n[" . $data['name'] . "]\n";
                    if (fwrite($fd, $line) === false) {
                        echo "Impossible to write to the file ($buddyfile)";
                        break;
                    } else {
                        $line = '';

                        $line .= 'host=' . $data['host'] . "\n";

                        $line .= 'fromdomain=' . $data['host'] . "\n";
                        $line .= 'accountcode=' . $data['accountcode'] . "\n";
                        $line .= 'disallow=' . $data['disallow'] . "\n";

                        $codecs = explode(",", $data['allow']);
                        foreach ($codecs as $codec) {
                            $line .= 'allow=' . $codec . "\n";
                        }

                        if (strlen($data['context']) > 1) {
                            $line .= 'context=' . $data['context'] . "\n";
                        }

                        if (strlen($data['dtmfmode']) > 1) {
                            $line .= 'dtmfmode=' . $data['dtmfmode'] . "\n";
                        }

                        if (strlen($data['insecure']) > 1) {
                            $line .= 'insecure=' . $data['insecure'] . "\n";
                        }

                        if (strlen($data['nat']) > 1) {
                            $line .= 'nat=' . $data['nat'] . "\n";
                        }

                        if (strlen($data['qualify']) > 1) {
                            $line .= 'qualify=' . $data['qualify'] . "\n";
                        }

                        if (strlen($data['type']) > 1) {
                            $line .= 'type=' . $data['type'] . "\n";
                        }

                        if (strlen($data['regexten']) > 1) {
                            $line .= 'regexten=' . $data['regexten'] . "\n";
                        }

                        if (strlen($data['amaflags']) > 1) {
                            $line .= 'amaflags=' . $data['amaflags'] . "\n";
                        }

                        if (strlen($data['language']) > 1) {
                            $line .= 'language=' . $data['language'] . "\n";
                        }

                        if (strlen($data['username']) > 1) {
                            $line .= 'username=' . $data['username'] . "\n";
                        }

                        if (strlen($data['fromuser']) > 1) {
                            $line .= 'fromuser=' . $data['fromuser'] . "\n";
                        }

                        if (strlen($data['callerid']) > 1) {
                            $line .= 'callerid=' . $data['callerid'] . "\n";
                        }

                        if (strlen($data['secret']) > 1) {
                            $line .= 'secret=' . $data['secret'] . "\n";
                        }

                        if ($data['calllimit'] > 0) {
                            $line .= 'call-limit=' . $data['calllimit'] . "\n";
                        }

                        if (fwrite($fd, $line) === false) {
                            echo gettext("Impossible to write to the file") . " ($buddyfile)";
                            break;
                        }
                    }
                }
                fclose($fd);
            }
        }

        AsteriskAccess::instance()->iaxReload();

    }

}
