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
require_once 'Magecheckout/Twitter/TwitterOauth.php';

class Magecheckout_SocialLogin_Model_Twitter_Oauth_Api extends Varien_Object
{
    protected $_twitter;

    /**
     * Init Twitter Oauth
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * @param null $token
     * @param null $tokenSecret
     */
    public function initTwitter($token = null, $tokenSecret = null)
    {
        $consumerKey    = Mage::helper('sociallogin/twitter')->getConsumerKey();
        $consumerSecret = Mage::helper('sociallogin/twitter')->getConsumerSecret();
        $this->_twitter = new TwitterOAuth($consumerKey, $consumerSecret, $token, $tokenSecret);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTwitter()
    {
        return $this->_twitter;
    }

    /**
     * get Twitter request token
     *
     * @return mixed
     */
    public function getRequestToken()
    {
        $authUrl = Mage::helper('sociallogin/twitter')->getAuthUrl();

        return $this->getTwitter()->getRequestToken($authUrl);
    }

    /**
     * get AuthorizeURL
     *
     * @return mixed
     */
    public function getAuthorizeURL()
    {
        $url = '';

        try {
            $requestToken = $this->getRequestToken();
            $session      = Mage::getSingleton('core/session');
            $session->setData('twitter_token', $requestToken['oauth_token']);
            $session->setData('twitter_token_secret', $requestToken['oauth_token_secret']);
            if ($this->getTwitter()->http_code == '200') {
                $url = $this->getTwitter()->getAuthorizeURL($requestToken['oauth_token']);
            }
        } catch (Exception $e) {
        }

        return $url;
    }
}