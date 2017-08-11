<?php
/**
 * Acoes do modulo "Pausas".
 *
 * MagnusSolution.com <info@magnussolution.com>
 * 28/10/2012
 */

class PortabilidadeCodigosController extends BaseController
{
    public $attributeOrder     = 't.favorito DESC, t.prefix ASC, t.company ASC';
    public $nameModelRelated   = 'PortabilidadeTrunk';
    public $nameFkRelated      = 'id_codigo';
    public $nameOtherFkRelated = 'id_trunk';
    public $filterByUser       = false;
    public $group              = 'company';

    public function init()
    {
        $this->instanceModel = new PortabilidadeCodigos;
        $this->abstractModel = PortabilidadeCodigos::model();

        $this->abstractModelRelated = PortabilidadeTrunk::model();
        parent::init();
    }

    public function actionImportFromCsv()
    {
        ini_set("memory_limit", "1024M");
        ini_set("upload_max_filesize", "3M");
        ini_set("max_execution_time", "90");

        $interpreter = new CSVInterpreter($_FILES['file']['tmp_name']);
        $array       = $interpreter->toArray();
        $errors      = array();
        if ($array) {
            $recorder = new CSVACtiveRecorder($array, 'PortabilidadeCodigos');
            if ($recorder->save()) {
                $info = 'IMPORT ' . getNumberOfLinesFromFile($_FILES['file']['tmp_name']) . ' CODIGOS RN1 ';
                MagnusLog::insertLOG('import', $info);
            } else {
                $errors = $recorder->getErrors();
            }

        } else {
            $errors = $interpreter->getErrors();
        }

        echo json_encode(array(
            $this->nameSuccess => count($errors) > 0 ? false : true,
            $this->nameMsg     => count($errors) > 0 ? implode(',', $errors) : $this->msgSuccess,
        ));
    }

    private function getNumberOfLinesFromFile($filename)
    {
        try {
            $file = new \SplFileObject('filename', 'r');
            $file->seek(PHP_INT_MAX);
            return $file->key() + 1;

        } catch (Exception $e) {
            return 0;
        }
    }

}
