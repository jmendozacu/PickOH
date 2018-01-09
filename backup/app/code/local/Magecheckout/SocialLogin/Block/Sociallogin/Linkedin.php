<?php

class Magecheckout_SocialLogin_Block_Sociallogin_Linkedin extends Magecheckout_SocialLogin_Block_Sociallogin
{
    public function isEnabled()
    {
        if (Mage::helper('sociallogin')->isEnabled() && Mage::helper('sociallogin/linkedin')->isEnabled()) {
            return true;
        } else {
            return false;
        }
    }
    public function getLoginUrl()
    {
        return $this->getUrl('sociallogin/authentication_linkedin/login');
    }
}