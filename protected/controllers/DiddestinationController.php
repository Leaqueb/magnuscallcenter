<?php
/**
 * Acoes do modulo "Diddestination".
 *
 * =======================================
 * ###################################
 * MagnusBilling
 *
 * @package MagnusBilling
 * @author Adilson Leffa Magnus.
 * @copyright Copyright (C) 2005 - 2016 MagnusBilling. All rights reserved.
 * ###################################
 *
 * This software is released under the terms of the GNU Lesser General Public License v2.1
 * A copy of which is available from http://www.gnu.org/copyleft/lesser.html
 *
 * Please submit bug reports, patches, etc to https://github.com/magnusbilling/mbilling/issues
 * =======================================
 * Magnusbilling.com <info@magnusbilling.com>
 * 24/09/2012
 */

class DiddestinationController extends BaseController
{
    public $attributeOrder = 't.id';
    public $extraValues    = array(
        'idUser'     => 'username',
        'idIvr'      => 'name',
        'idCampaign' => 'name',
    );

    public $fieldsFkReport = array(
        'id_ivr'  => array(
            'table'       => 'pkg_ivr',
            'pk'          => 'id',
            'fieldReport' => 'name',
        ), 'id_campaign' => array(
            'table'       => 'pkg_campaign',
            'pk'          => 'id',
            'fieldReport' => 'name',
        ),
        'id_user' => array(
            'table'       => 'pkg_user',
            'pk'          => 'id',
            'fieldReport' => 'username',
        ),

    );

    public function init()
    {
        $this->instanceModel = new Diddestination;
        $this->abstractModel = Diddestination::model();
        $this->titleReport   = Yii::t('yii', 'Did Destination');
        parent::init();
    }

}
