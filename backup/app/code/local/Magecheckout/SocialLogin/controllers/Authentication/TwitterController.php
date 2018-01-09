<?php
require_once 'Magecheckout/SocialLogin/controllers/AuthenticationController.php';

class Magecheckout_SocialLogin_Authentication_TwitterController extends Magecheckout_SocialLogin_AuthenticationController
{
    const SOCIAL_API_TYPE = 'twitter';

    /**
     * Callback action after validation
     */
    public function callbackAction()
    {
        $denied = $this->getRequest()->getParam('denied');
        if (isset($denied)) {
            $this->_appendJsToHead("window.opener.location.reload(true);window.close();");
            $this->addError('Login failed as you have not granted access.');

            return;
        }
        $session       = Mage::getSingleton('core/session');
        $token         = $session->getData('twitter_token');
        $tokenSecret   = $session->getData('twitter_token_secret');
        $requestToken  = $this->getRequest()->getParam('oauth_token');
        $oauthVerifier = $this->getRequest()->getParam('oauth_verifier');
        $loginRedirect = $this->_loginPostRedirect();
        if (isset($requestToken) && $requestToken == $token) {
            $twitterModel = Mage::getSingleton('sociallogin/twitter_oauth_api')->initTwitter($token, $tokenSecret);
            $twitter      = $twitterModel->getTwitter();
            $twitter->getAccessToken($oauthVerifier);
            if ($twitter->http_code == '200') {
                $info = $twitter->get('account/verify_credentials');
                if ($tokenId = $info->id) {
                    $author = $this->getAuthorByTokenId($tokenId, self::SOCIAL_API_TYPE);
                    if ($author && $author->getId()) {
                        $customer = Mage::getModel('customer/customer')->load($author->getCustomerId());
                    } else {
                        $fullName = explode(' ', $info->name);
                        $data     = array(
                            'firstname' => $fullName[0],
                            'lastname'  => $fullName[1],
                            'email'     => $tokenId . '@'.self::SOCIAL_API_TYPE.'.com'
                        );
                        $customer = $this->createCustomerAccount($data);
                        if ($this->getTwitterHelper()->sendPassword()) {
                            $customer->sendPasswordReminderEmail();
                        }
                        $this->createAuthor($tokenId, $customer->getId(), self::SOCIAL_API_TYPE);
                        $notice        = 'Please update your contact details.';
                        $loginRedirect = $this->getCustomerEditUrl();
                    }
                    $this->getSession()->setCustomerAsLoggedIn($customer);
                    $this->_appendJsToHead("window.opener.location.href='$loginRedirect';window.close();");
                    if (isset($notice)) {
                        $this->addNotice($notice);
                    }

                    return;
                }
            }
        }

    }

    public function unsetToken()
    {
        $session = Mage::getSingleton('core/session');
        $session->setData('twitter_token', '');
        $session->setData('twitter_token_secret', '');
    }

    /**
     * @return mixed
     */
    public function getTwitterHelper()
    {
        return $this->getHelperApi('twitter');
    }
}