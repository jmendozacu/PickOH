<?php
class Magecheckout_SocialLogin_Model_Mysql4_Author extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('sociallogin/author', 'entity_id');
    }

}