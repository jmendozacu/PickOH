<?php

class Magecheckout_SocialLogin_Block_Generator_Css extends Mage_Core_Block_Template
{
    protected $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('sociallogin');

        return parent::__construct();
    }

}