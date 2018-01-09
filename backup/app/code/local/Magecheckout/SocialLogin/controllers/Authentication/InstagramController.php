<?php
require_once 'Magecheckout/SocialLogin/controllers/AuthenticationController.php';

class Magecheckout_SocialLogin_Authentication_InstagramController extends Magecheckout_SocialLogin_AuthenticationController
{
    const SOCIAL_API_TYPE = 'instagram';

    /**
     *  Instagram response
     */
    public function callbackAction()
    {
        $loginRedirect = $this->_loginPostRedirect();
        $instagram     = Mage::getSingleton('sociallogin/instagram_oauth_api')->getInstagram();
        $code          = $this->getRequest()->getParam('code');
        if (!$code) {
            $this->_appendJsToHead("window.close();");
            $this->addError('You have not grant permission');
        }
        $info = $instagram->getOAuthToken($code)->user;
        if ($tokenId = $info->id) {
            $author = $this->getAuthorByTokenId($tokenId, self::SOCIAL_API_TYPE);
            if ($author && $author->getId()) {
                $customerId = $author->getCustomerId();
                $customer   = Mage::getModel('customer/customer')->load($customerId);
            } else {
                $fullName = explode(' ', $info->full_name);
                $data     = array(
                    'firstname' => $fullName[0],
                    'lastname'  => $fullName[1],
                    'email'     => $tokenId . '@' . self::SOCIAL_API_TYPE . '.com'
                );
                $customer = $this->createCustomerAccount($data);
                $this->createAuthor($tokenId, $customer->getId(), self::SOCIAL_API_TYPE);
                $loginRedirect = $this->getCustomerEditUrl();
                $notice        = 'Please update your contact details.';
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