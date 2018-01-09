<?php

class Magecheckout_SocialLogin_Helper_Google extends Magecheckout_SocialLogin_Helper_Data
{
    const XML_PATH_GOOGLE_ENABLED = 'sociallogin/google/is_enabled';
    const XML_PATH_GOOGLE_CLIENT_ID = 'sociallogin/google/client_id';
    const XML_PATH_GOOGLE_CLIENT_SECRET = 'sociallogin/google/client_secret';
    const XML_PATH_GOOGLE_REDIRECT_URL = 'sociallogin/google/redirect_url';
    const XML_PATH_GOOGLE_SEND_PASSWORD = 'sociallogin/google/send_password';

    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GOOGLE_ENABLED, $storeId);
    }

    public function getClientId($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GOOGLE_CLIENT_ID, $storeId);
    }

    public function getClientSecret($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GOOGLE_CLIENT_SECRET, $storeId);
    }

    public function getRedirectUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GOOGLE_REDIRECT_URL, $storeId);
    }

    public function sendPassword($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GOOGLE_SEND_PASSWORD, $storeId);
    }

    public function getAuthUrl()
    {
        $url = Mage::getUrl('sociallogin/authentication_google/callback', array('_secure' => $this->isSecure()));
        //Remove index.php
        $url = str_replace('index.php/', '', $url);
        //Remove Session
        $url = str_replace('?___SID=U', '', $url);

        return $url;
    }
}