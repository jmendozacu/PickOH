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
class Magecheckout_SocialLogin_Model_Facebook_Oauth2_Api extends Varien_Object
{
    protected $_facebook;

    /**
     * Init Facebook Authentication Object
     */
    public function __construct()
    {
        $this->_facebook = new Magecheckout_Facebook_Authentication(array(
            'appId'  => Mage::helper('sociallogin/facebook')->getAppId(),
            'secret' => Mage::helper('sociallogin/facebook')->getAppSecret(),
            'cookie' => true,
        ));

    }

    /**
     * get Facebook Api Object Model
     *
     * @return mixed
     */
    public function getFacebook()
    {
        return $this->_facebook;
    }

    /**
     * get Facebook Login Url
     *
     * @return mixed
     */
    public function getLoginUrl()
    {
        $loginUrl = $this->getFacebook()->getLoginUrl(
            array(
                'display'      => 'popup',
                'redirect_uri' => Mage::helper('sociallogin/facebook')->getAuthUrl(),
                'scope'        => 'email',
            )
        );

        return $loginUrl;

    }

    public function getFacebookInfo()
    {
        $info   = array();
        $userId = $this->getFacebook()->getUser();
        if ($userId) {
            try {
                $info = $this->getFacebook()->api('/me?fields=id,name,first_name,last_name,email');
            } catch (Exception $e) {

            }
        }

        return $info;
    }
}