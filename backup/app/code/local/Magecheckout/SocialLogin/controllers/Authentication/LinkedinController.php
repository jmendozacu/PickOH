<?php

require_once 'Magecheckout/SocialLogin/controllers/AuthenticationController.php';

class Magecheckout_SocialLogin_Authentication_LinkedinController extends Magecheckout_SocialLogin_AuthenticationController
{
    const SOCIAL_API_TYPE = 'linkedin';

    /**
     * Linked Login Action
     */
    public function loginAction()
    {
        $helper        = $this->_getHelper();
        $loginRedirect = $this->_loginPostRedirect();
        $modelLinkedin = Mage::getModel('sociallogin/linkedin_oauth2_api');
        $client        = $modelLinkedin->getClient();
        if (strlen($client->client_id) == 0 || strlen($client->client_secret) == 0) {
            $this->_appendJsToHead("window.opener.location.reload(true);window.close();");
            if ($client->debug) {
                $application_line = __LINE__;
                $this->addError('Please go to LinkedIn Apps page https://www.linkedin.com/secure/developer?newapp= , ' .
                    'create an application, and in the line ' . $application_line .
                    ' set the client_id to Consumer key and client_secret with Consumer secret. ' .
                    'The Callback URL must be ' . $client->redirect_uri . ' Make sure you enable the ' .
                    'necessary permissions to execute the API calls your application needs.');
            }

            return;
        } else {
            if (($success = $client->Initialize())) {
                if (($success = $client->Process())) {
                    if (strlen($client->authorization_error)) {
                        $client->error = $client->authorization_error;
                        $success       = false;
                    } elseif (strlen($client->access_token)) {
                        $success = $client->CallAPI(
                            'http://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,location,picture-url,public-profile-url,formatted-name)',
                            'GET', array(
                            'format' => 'json'
                        ), array('FailOnAccessError' => true), $info);
                    }
                }
                $success = $client->Finalize($success);
            }
            if ($client->exit) {
                exit;
            };
            if ($success) {
                if ($email = $info->emailAddress) {
                    $customer = $this->getCustomerByEmailAction($email);
                    if (!$customer || !$customer->getId()) {
                        $data     = array(
                            'firstname' => $info->firstName,
                            'lastname'  => $info->lastName,
                            'email'     => $info->emailAddress
                        );
                        $customer = $this->createCustomerAccount($data);
                        if ($helper->sendPassword()) {
                            $customer->sendPasswordReminderEmail();
                        }
                    }
                }
                $this->getSession()->setCustomerAsLoggedIn($customer);
                $this->_appendJsToHead("window.opener.location.href='$loginRedirect';window.close();");
            } else {
                $this->_appendJsToHead("window.opener.location.reload(true);window.close();");
                $this->addError($client->error);

                return;
            }
        }

        return;
    }

    protected function _getHelper()
    {
        return Mage::helper('sociallogin/linkedin');
    }
}