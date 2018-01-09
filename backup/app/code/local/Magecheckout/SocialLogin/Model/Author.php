<?php

class Magecheckout_SocialLogin_Model_Author extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('sociallogin/author');
    }


}
