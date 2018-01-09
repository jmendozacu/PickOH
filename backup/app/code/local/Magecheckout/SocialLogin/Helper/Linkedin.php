<?php

class Magecheckout_SocialLogin_Helper_Linkedin extends Magecheckout_SocialLogin_Helper_Data
{
    const XML_PATH_LINKEDIN_ENABLED = 'sociallogin/linkedin/is_enabled';
    const XML_PATH_LINKEDIN_CLIENT_ID = 'sociallogin/linkedin/client_id';
    const XML_PATH_LINKEDIN_CLIENT_SECRET = 'sociallogin/linkedin/client_secret';
    const XML_PATH_LINKEDIN_SEND_PASSWORD = 'sociallogin/linkedin/send_password';
    const XML_PATH_LINKEDIN_CALLBACK_URIS = 'sociallogin/linkedin/redirect_url';

    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_LINKEDIN_ENABLED, $storeId);
    }

    public function getClientId($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_LINKEDIN_CLIENT_ID, $storeId);
    }

    public function getClientSecret($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_LINKEDIN_CLIENT_SECRET, $storeId);
    }

    public function getRedirectUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_LINKEDIN_CALLBACK_URIS, $storeId);
    }

    public function sendPassword($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_LINKEDIN_SEND_PASSWORD, $storeId);
    }

    public function getAuthUrl()
    {
        $url = str_replace('index.php/', '', Mage::getUrl('sociallogin/authentication_linkedin/login', array('_secure' => $this->isSecure())));

        return $url;
    }
}