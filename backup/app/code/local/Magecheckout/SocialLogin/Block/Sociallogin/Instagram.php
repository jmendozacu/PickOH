<?php

class Magecheckout_SocialLogin_Block_Sociallogin_Instagram extends Magecheckout_SocialLogin_Block_Sociallogin
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        if (Mage::helper('sociallogin')->isEnabled() && Mage::helper('sociallogin/instagram')->isEnabled()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     * @throws Exeption
     */
    public function getLoginUrl()
    {
        $instagram = Mage::getSingleton('sociallogin/instagram_oauth_api')->getInstagram();
        $loginUrl  = $instagram->getLoginUrl();

        return $loginUrl;
    }
}