<?php
/**
 * Override default Controller methods.
 *
 * MagnusBilling <info@magnusbilling.com>
 * 11/05/2017
 */
class Controller extends BaseController
{
    public $nofilterPerAdminGroup = array(

        'module',
        'groupUserGroup',
        'groupUser',
        'campaignRestrictPhone',
        'provider',
        'trunk',
        'userType',
        'groupuser',
        'configuration',
    );

    public function authorizedNoSession()
    {
        $allow = array(
            'site',
            'authentication',
        );
        return in_array($this->controllerName, $allow);
    }

}
