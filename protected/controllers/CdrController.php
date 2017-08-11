<?php
/**
 * Acoes do modulo "Cdr".
 *
 * MagnusSolution.com <info@magnussolution.com>
 * 17/08/2012
 */

/*
atualizad a tegorização das ligaçoes em CDR
UPDATE `pkg_cdr` SET id_category =11 WHERE calledstation IN (
SELECT number
FROM pkg_phonenumber
WHERE id_category =11
)
 */

class CdrController extends BaseController
{
    public $attributeOrder = 'starttime DESC';
    public $extraValues    = array('idUser' => 'username',
        'idCampaign'                            => 'name',
        'idTrunk'                               => 'trunkcode',
        'idPhonebook'                           => 'name',
        'idCategory'                            => 'name',
    );
    public $join = 'JOIN pkg_user ON pkg_user.id = t.id_user
                            JOIN pkg_campaign ON pkg_campaign.id = t.id_campaign
                            JOIN pkg_category ON t.id_category = pkg_category.id';

    public $fieldsFkReport = array(
        'id_user'     => array(
            'table'       => 'pkg_user',
            'pk'          => 'id',
            'fieldReport' => "CONCAT(username, ' ', name) ",
        ),
        'id_campaign' => array(
            'table'       => 'pkg_campaign',
            'pk'          => 'id',
            'fieldReport' => 'name',
        ),
        'id_trunk'    => array(
            'table'       => 'pkg_trunk',
            'pk'          => 'id',
            'fieldReport' => 'trunkcode',
        ),
        'id_category' => array(
            'table'       => 'pkg_category',
            'pk'          => 'id',
            'fieldReport' => 'name',
        ),
    );

    public function init()
    {
        $this->instanceModel = new Cdr;
        $this->abstractModel = Cdr::model();
        $this->titleReport   = Yii::t('yii', 'Calls');
        parent::init();
    }

    public function actionDownloadRecord()
    {

        $ids = json_decode($_GET['ids']);

        $criteria = new CDbCriteria();
        $criteria->addInCondition('id', $ids);

        if (isset($_POST['filter']) && strlen($_POST['filter']) > 5) {
            $filter = $_POST['filter'];

            $filter = $this->createCondition(json_decode($filter));

            $whereTerminatecauseid = !preg_match("/terminatecauseid/", $filter) ? ' AND terminatecauseid = 1' : false;
            $filter                = $filter . $whereTerminatecauseid;

            $this->filter = $filter = $this->extraFilter($filter);

            $criteria->addCondition($this->filter);
            if (count($this->paramsFilter)) {
                foreach ($this->paramsFilter as $key => $value) {
                    $criteria->params[$key] = $value;
                }
            }
        }
        $modelCdr = Cdr::model()->findAll($criteria);

        $folder       = $this->magnusFilesDirectory . 'monitor';
        $record_patch = $this->config['global']['record_patch'];

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        array_map('unlink', glob("$folder/*"));

        if (count($modelCdr)) {
            foreach ($modelCdr as $records) {
                $number   = $records->calledstation;
                $day      = $records->starttime;
                $uniqueid = $records->uniqueid;
                $day      = explode(' ', $day);
                $day      = explode('-', $day[0]);

                $day = $day[2] . $day[1] . $day[0];

                exec('cp -rf  ' . $record_patch . '/' . $day . '/*.' . $uniqueid . '.gsm ' . $folder);
            }
            exec("cd $folder && tar -czf records_" . Yii::app()->session['username'] . ".tar.gz *");

            $file_name = 'records_' . Yii::app()->session['username'] . '.tar.gz';
            $path      = $folder . '/' . $file_name;
            header('Content-type: application/tar+gzip');

            echo json_encode(array(
                $this->nameSuccess => true,
                $this->nameMsg     => 'success',
            ));

            header('Content-Description: File Transfer');
            header("Content-Type: application/x-tar");
            header('Content-Disposition: attachment; filename=' . basename($file_name));
            header("Content-Transfer-Encoding: binary");
            header('Accept-Ranges: bytes');
            header('Content-type: application/force-download');
            ob_clean();
            flush();
            if (readfile($path)) {
                unlink($path);
            }
            exec("rm -rf $folder/*");

        } else {
            echo json_encode(array(
                $this->nameSuccess => false,
                $this->nameMsg     => 'Audio no found',
            ));
            exit;
        }
    }

    public function extraFilterCustomClient($filter)
    {

        $sql = "SELECT id_campaign FROM pkg_user_campaign WHERE id_user = " . Yii::app()->session['id_user'];

        $filter .= ' AND id_campaign IN ( ' . $sql . ')';

        return $filter;
    }

}
