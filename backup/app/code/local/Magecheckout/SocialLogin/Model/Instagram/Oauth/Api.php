<?php
require_once 'Magecheckout/Instagram/Instagram.php';

class Magecheckout_SocialLogin_Model_Instagram_Oauth_Api
{
    protected $instagram;

    /**
     * init Instagram Oauth
     */
    public function __construct()
    {
        $helperInstagram = Mage::helper('sociallogin/instagram');
        $this->instagram = new Instagram(
            array(
                'apiKey'      => $helperInstagram->getClientId(),
                'apiSecret'   => $helperInstagram->getClientSecret(),
                'apiCallback' => $helperInstagram->getAuthUrl(),
            ));
    }

    /**
     * get Instagram Oauth
     *
     * @return Instagram
     */
    public function getInstagram()
    {
        return $this->instagram;
    }
}