<?php
/**
 * Acoes do modulo "PhoneNumber".
 *
 * MagnusSolution.com <info@magnussolution.com>
 * 28/10/2012
 */

class MassiveCallPhoneNumberController extends BaseController
{
    public $attributeOrder = 't.id';
    public $extraValues    = array('idMassiveCallPhonebook' => 'name');

    public $fieldsFkReport = array(
        'id_phonebook' => array(
            'table'       => 'pkg_phonebook',
            'pk'          => 'id',
            'fieldReport' => 'name',
        ),
    );

    public function init()
    {
        $this->instanceModel = new MassiveCallPhoneNumber;
        $this->abstractModel = MassiveCallPhoneNumber::model();
        $this->titleReport   = Yii::t('yii', 'Massive Call Phone Number');

        parent::init();
    }

    public function actionImportFromCsv()
    {
        ini_set("memory_limit", "1024M");
        ini_set("upload_max_filesize", "25M");
        ini_set("max_execution_time", "190");

        $interpreter      = new CSVInterpreter($_FILES['file']['tmp_name']);
        $array            = $interpreter->toArray();
        $additionalParams = [['key' => 'id_massive_call_phonebook', 'value' => $_POST['id_massive_call_phonebook']]];
        $errors           = array();
        if ($array) {
            $recorder = new CSVACtiveRecorder($array, 'MassiveCallPhoneNumber', $additionalParams);
            if ($recorder->save());
            $errors = $recorder->getErrors();

        } else {
            $errors = $interpreter->getErrors();
        }

        echo json_encode(array(
            $this->nameSuccess => count($errors) > 0 ? false : true,
            $this->nameMsg     => count($errors) > 0 ? implode(',', $errors) : $this->msgSuccess,
        ));
    }
}
