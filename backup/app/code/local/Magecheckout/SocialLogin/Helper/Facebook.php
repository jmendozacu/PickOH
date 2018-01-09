<?php

class Magecheckout_SocialLogin_Helper_Facebook extends Magecheckout_SocialLogin_Helper_Data
{
    const XML_PATH_FACEBOOK_ENABLED = 'sociallogin/facebook/is_enabled';
    const XML_PATH_FACEBOOK_APP_ID = 'sociallogin/facebook/app_id';
    const XML_PATH_FACEBOOK_APP_SECRET = 'sociallogin/facebook/app_secret';
    const XML_PATH_FACEBOOK_REDIRECT_URL = 'sociallogin/facebook/redirect_url';
    const XML_PATH_FACEBOOK_SEND_PASSWORD = 'sociallogin/facebook/send_password';

    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FACEBOOK_ENABLED, $storeId);
    }

    public function getAppId($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FACEBOOK_APP_ID, $storeId);
    }

    public function sendPassword($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FACEBOOK_SEND_PASSWORD, $storeId);
    }

    public function getAppSecret($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FACEBOOK_APP_SECRET, $storeId);
    }

    public function getRedirectUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FACEBOOK_REDIRECT_URL, $storeId);
    }

    public function getAuthUrl()
    {
        $url = str_replace('index.php/', '', Mage::getUrl('sociallogin/authentication_facebook/callback', array('_secure' => $this->isSecure(), 'auth' => 1)));

        return $url;
    }


}