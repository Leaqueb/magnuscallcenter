<?php
/**
 * Acoes do modulo "Campaign".
 *
 * MagnusSolution.com <info@magnussolution.com>
 * 28/10/2012
 */

class CategoryController extends BaseController
{
    public $attributeOrder = 't.id';

    public function init()
    {
        $this->instanceModel = new Category;
        $this->abstractModel = Category::model();

        parent::init();
    }

    public function extraFilterCustom($filter)
    {
        $filter = !preg_match("/status/", $filter) ? ' status = 1' : false;

        if (Yii::app()->session['isOperator']) {
            $filter = $this->extraFilterCustomOperator($filter);
        } else if (Yii::app()->session['isClient']) {
            $filter = $this->extraFilterCustomClient($filter);
        }

        return $filter;
    }

}
