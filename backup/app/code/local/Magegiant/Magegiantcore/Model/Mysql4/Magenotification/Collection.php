<?php

class Magegiant_Magegiantcore_Model_Mysql4_Magenotification_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('magegiantcore/magegiantcore');
    }
}