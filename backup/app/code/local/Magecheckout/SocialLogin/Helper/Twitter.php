<?php

class Magecheckout_SocialLogin_Helper_Twitter extends Magecheckout_SocialLogin_Helper_Data
{
    const XML_PATH_TWITTER_ENABLED = 'sociallogin/twitter/is_enabled';
    const XML_PATH_TWITTER_CONSUMER_KEY = 'sociallogin/twitter/consumer_key';
    const XML_PATH_TWITTER_CONSUMER_SECRET = 'sociallogin/twitter/consumer_secret';
    const XML_PATH_TWITTER_SEND_PASSWORD = 'sociallogin/twitter/send_password';
    const XML_PATH_TWITTER_CALLBACK_URIS = 'sociallogin/twitter/redirect_url';

    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TWITTER_ENABLED, $storeId);
    }

    public function getConsumerKey($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TWITTER_CONSUMER_KEY, $storeId);
    }

    public function getConsumerSecret($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TWITTER_CONSUMER_SECRET, $storeId);
    }

    public function getRedirectUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TWITTER_CALLBACK_URIS, $storeId);
    }

    public function sendPassword($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TWITTER_SEND_PASSWORD, $storeId);
    }

    public function getAuthUrl()
    {
        $url = str_replace('index.php/', '', Mage::getUrl('sociallogin/authentication_twitter/callback', array('_secure' => $this->isSecure())));

        return $url;
    }
}