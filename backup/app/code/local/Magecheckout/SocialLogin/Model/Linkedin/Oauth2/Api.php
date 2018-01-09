<?php
require_once 'Magecheckout/Linkedin/http.php';
require_once 'Magecheckout/Linkedin/oauth_client.php';

class Magecheckout_SocialLogin_Model_Linkedin_Oauth2_Api
{
    protected $client;

    public function __construct()
    {
        $helper                      = Mage::helper('sociallogin/linkedin');
        $this->client                = new oauth_client_class;
        $this->client->debug         = false;
        $this->client->debug_http    = true;
        $this->client->client_id     = $helper->getClientId();
        $this->client->client_secret = $helper->getClientSecret();
        $this->client->redirect_uri  = $helper->getAuthUrl();
        $this->client->scope         = 'r_basicprofile r_emailaddress';
    }

    public function getClient()
    {
        return $this->client;
    }
}