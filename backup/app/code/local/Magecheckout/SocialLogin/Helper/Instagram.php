<?php

class Magecheckout_SocialLogin_Helper_Instagram extends Magecheckout_SocialLogin_Helper_Data
{
    const XML_PATH_INSTAGRAM_ENABLED = 'sociallogin/instagram/is_enabled';
    const XML_PATH_INSTAGRAM_CLIENT_ID = 'sociallogin/instagram/client_id';
    const XML_PATH_INSTAGRAM_CLIENT_SECRET = 'sociallogin/instagram/client_secret';
    const XML_PATH_INSTAGRAM_REDIRECT_URL = 'sociallogin/instagram/redirect_url';
    const XML_PATH_INSTAGRAM_SEND_PASSWORD = 'sociallogin/instagram/send_password';

    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_ENABLED, $storeId);
    }

    public function getClientId($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_CLIENT_ID, $storeId);
    }

    public function getClientSecret($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_CLIENT_SECRET, $storeId);
    }

    public function getRedirectUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_REDIRECT_URL, $storeId);
    }

    public function sendPassword($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_SEND_PASSWORD, $storeId);
    }

    public function getAuthUrl()
    {
        $url = str_replace('index.php/', '', Mage::getUrl('sociallogin/authentication_instagram/callback', array('_secure' => $this->isSecure())));

        return $url;
    }

}