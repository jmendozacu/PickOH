<?php

/**
 * MageGiant
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magegiant.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magegiant.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @copyright   Copyright (c) 2014 Magegiant (http://magegiant.com/)
 * @license     http://magegiant.com/license-agreement.html
 */
require_once 'Magecheckout/Oauth2/Google_Client.php';
require_once 'Magecheckout/Oauth2/contrib/Google_Oauth2Service.php';

class Magecheckout_SocialLogin_Model_Google_Oauth2_Api extends Varien_Object
{
    protected $_google;
    protected $_oauth2;

    /**
     * Init Google Client Oauth2
     */
    public function __construct()
    {
        $this->_google = new Google_Client(
            array(
                'oauth2_client_id'     => Mage::helper('sociallogin/google')->getClientId(),
                'oauth2_client_secret' => Mage::helper('sociallogin/google')->getClientSecret(),
                'oauth2_redirect_uri'  => Mage::helper('sociallogin/google')->getAuthUrl(),
            )
        );
        $this->_oauth2 = new Google_Oauth2Service($this->_google);
    }

    /**
     * get Google Oauth2 Object
     *
     * @return mixed
     */
    public function getGoogle()
    {
        return $this->_google;
    }

    /**
     * get Oauth2
     */
    public function getOauth2()
    {
        return $this->_oauth2;
    }
}