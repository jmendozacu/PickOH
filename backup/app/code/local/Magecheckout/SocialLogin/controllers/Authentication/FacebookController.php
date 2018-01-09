<?php
require_once 'Magecheckout/SocialLogin/controllers/AuthenticationController.php';

class Magecheckout_SocialLogin_Authentication_FacebookController extends Magecheckout_SocialLogin_AuthenticationController
{
    const SOCIAL_API_TYPE = 'facebook';

    /**
     * Callback action after validation
     */
    public function callbackAction()
    {
        $isAuth        = $this->getRequest()->getParam('auth', 0);
        $errorReason   = $this->getRequest()->getParam('error_reason', '');
        $facebook      = Mage::getSingleton('sociallogin/facebook_oauth2_api')->getFacebook();
        $userId        = $facebook->getUser();
        $loginRedirect = $this->_loginPostRedirect();
        if ($isAuth) {
            if (!$userId) {
                if ($errorReason == 'user_denied') {
                    $this->_appendJsToHead("window.close();");
                    $this->addError('You have not grant permission');

                    return;
                } else {
                    $loginUrl = $facebook->getLoginUrl(array('scope' => 'email'));
                    $this->_appendJsToHead("window.location.href='$loginUrl'");

                    return;
                }
            }
            $info = Mage::getSingleton('sociallogin/facebook_oauth2_api')->getFacebookInfo();
            if (!empty($info)) {
                if (isset($info['email'])) {
                    $customer = $this->getCustomerByEmailAction($info['email']);
                    if (!$customer || !$customer->getId()) {
                        $data     = array(
                            'firstname' => $info['first_name'],
                            'lastname'  => $info['last_name'],
                            'email'     => $info['email']);
                        $customer = $this->createCustomerAccount($data);
                        if ($this->getFacebookHelper()->sendPassword()) {
                            $customer->sendPasswordReminderEmail();
                        }
                    }
                    $this->getSession()->setCustomerAsLoggedIn($customer);
                    $this->_appendJsToHead("window.opener.location.href='$loginRedirect';window.close();");
                } else if (isset($info['id'])) {
                    $author = $this->getAuthorByTokenId($info['id'], self::SOCIAL_API_TYPE);
                    if ($author && $author->getId()) {
                        $customer = Mage::getModel('customer/customer')->load($author->getCustomerId());
                    } else {
                        $data     = array(
                            'firstname' => $info['first_name'],
                            'lastname'  => $info['last_name'],
                            'email'     => $info['id'] . '@' . self::SOCIAL_API_TYPE . '.com'
                        );
                        $customer = $this->createCustomerAccount($data);
                        if ($this->getFacebookHelper()->sendPassword()) {
                            $customer->sendPasswordReminderEmail();
                        }
                        $this->createAuthor($info['id'], $customer->getId(), self::SOCIAL_API_TYPE);
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

    /**
     * @return mixed
     */
    public function getFacebookHelper()
    {
        return $this->getHelperApi('facebook');
    }
}