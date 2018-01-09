<?php

class Magecheckout_SocialLogin_Block_Sociallogin_Facebook extends Magecheckout_SocialLogin_Block_Sociallogin
{
    /**
     * Check Facebook is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (Mage::helper('sociallogin')->isEnabled() && Mage::helper('sociallogin/facebook')->isEnabled()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get Facebook login url
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return Mage::getSingleton('sociallogin/facebook_oauth2_api')->getLoginUrl();
    }
}