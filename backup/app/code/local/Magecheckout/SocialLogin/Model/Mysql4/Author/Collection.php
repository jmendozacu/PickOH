<?php
class Magecheckout_SocialLogin_Model_Mysql4_Author_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('sociallogin/author');
    }

}