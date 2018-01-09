<?php
require_once 'Magecheckout/SocialLogin/controllers/AuthenticationController.php';

class Magecheckout_SocialLogin_Authentication_GoogleController extends Magecheckout_SocialLogin_AuthenticationController
{


    /**
     * Callback action after validation
     */
    public function callbackAction()
    {
        $code          = $this->getRequest()->getParam('code');
        $loginRedirect = $this->_loginPostRedirect();
        if (!$code) {
            $this->_appendJsToHead("window.opener.location.reload(true);window.close();");
            $this->addError('Login failed as you have not granted access.');

            return;
        }
        $google       = Mage::getSingleton('sociallogin/google_oauth2_api');
        $googleClient = $google->getGoogle();
        $googleClient->authenticate();
        if ($googleClient->getAccessToken()) {
            $oauth2 = $google->getOauth2();
            $info   = $oauth2->userinfo->get();
            if (!empty($info)) {
                if (isset($info['email'])) {
                    $customer = $this->getCustomerByEmailAction($info['email']);
                    if (!$customer || !$customer->getId()) {
                        $fullName = explode(' ', $info['name']);
                        $data     = array(
                            'firstname' => $fullName[0],
                            'lastname'  => $fullName[1],
                            'email'     => $info['email']);
                        $customer = $this->createCustomerAccount($data);
                        if ($this->getGoogleHelper()->sendPassword()) {
                            $customer->sendPasswordReminderEmail();
                        }

                    }
                    $this->getSession()->setCustomerAsLoggedIn($customer);
                    $this->_appendJsToHead("window.opener.location.href='$loginRedirect';window.close();");

                    return;
                }
            }
        }

        return;
    }

    /**
     * @return mixed
     */
    public function getGoogleHelper()
    {
        return $this->getHelperApi('google');
    }
}