<?php

class Magecheckout_SocialLogin_Block_Sociallogin_Google extends Magecheckout_SocialLogin_Block_Sociallogin
{
    /**
     * Check Google Login is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (Mage::helper('sociallogin')->isEnabled() && Mage::helper('sociallogin/google')->isEnabled()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get Google Login Url
     *
     * @return mixed
     */
    public function getLoginUrl()
    {
        $scope        = array(
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/userinfo.email'
        );
        $googleClient = Mage::getSingleton('sociallogin/google_oauth2_api')->getGoogle();
        $googleClient->setScopes($scope);

        return $googleClient->createAuthUrl();
    }
}