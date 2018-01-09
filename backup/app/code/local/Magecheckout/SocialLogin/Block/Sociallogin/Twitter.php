<?php

class Magecheckout_SocialLogin_Block_Sociallogin_Twitter extends Magecheckout_SocialLogin_Block_Sociallogin
{
    public function isEnabled()
    {
        if (Mage::helper('sociallogin')->isEnabled() && Mage::helper('sociallogin/twitter')->isEnabled()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get Twitter popup url
     *
     * @return mixed
     */
    public function getLoginUrl()
    {
        $twitter = Mage::getSingleton('sociallogin/twitter_oauth_api')->initTwitter();
        $url     = $twitter->getAuthorizeURL();

        return $url;
    }
}