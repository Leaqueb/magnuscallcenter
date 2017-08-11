<?php
/**
 * Acoes do modulo "Trunk".
 *
 * MagnusSolution.com <info@magnussolution.com>
 * 23/06/2012
 */

class TrunkController extends BaseController
{
    public $attributeOrder = 't.id';
    public $extraValues    = array('idProvider' => 'provider_name');

    public function init()
    {
        $this->instanceModel = new Trunk;
        $this->abstractModel = Trunk::model();
        $this->titleReport   = Yii::t('yii', 'Trunk');
        parent::init();
    }

    public function beforeSave($values)
    {
        if (isset($values['allow'])) {
            $values['allow'] = preg_replace("/,0/", "", $values['allow']);
            $values['allow'] = preg_replace("/0,/", "", $values['allow']);

        }
        return $values;
    }

    public function generateSipFile()
    {
        $select = 'trunkcode, providerip, user, secret, disallow, allow, directmedia, context, maxuse, dtmfmode, insecure, nat, qualify, type, host, register_string';
        $model  = Trunk::model()->findAll(
            array(
                'select' => $select,
            ));

        if (is_array($model) > 0) {
            AsteriskAccess::instance()->writeAsteriskFile($model, '/etc/asterisk/sip_magnus.conf', 'trunkcode');

        }
    }

    public function afterSave($model, $values)
    {
        $this->generateSipFile();
    }
    public function afterDestroy($values)
    {
        $this->generateSipFile();
    }

    public function setAttributesModels($attributes, $models)
    {
        $trunkRegister = AsteriskAccess::instance()->sipShowRegistry();
        $trunkRegister = explode("\n", $trunkRegister['data']);

        for ($i = 0; $i < count($attributes) && is_array($attributes); $i++) {
            $modelTrunk                                = Trunk::model()->findByPk((int) $attributes[$i]['failover_trunk']);
            $attributes[$i]['failover_trunktrunkcode'] = count($modelTrunk)
            ? $modelTrunk->trunkcode
            : Yii::t('yii', 'undefined');
            foreach ($trunkRegister as $key => $trunk) {
                if (preg_match("/" . $attributes[$i]['host'] . ".*" . $attributes[$i]['user'] . ".*Registered/", $trunk) && $attributes[$i]['providertech'] == 'sip') {
                    $attributes[$i]['registered'] = 1;
                    break;
                }
            }

        }

        return $attributes;
    }
}
