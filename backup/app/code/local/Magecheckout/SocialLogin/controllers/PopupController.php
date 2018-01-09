<?php

class Magecheckout_SocialLogin_PopupController extends Mage_Core_Controller_Front_Action
{
    /**
     * Process login ajax action when click Login Button
     *
     * @return json object
     */
    public function loginAction()
    {
        $flag          = true;
        $session       = $this->_getSession();
        $captchaStatus = $session->getResultCaptcha();
        $result        = array();
        if ($captchaStatus) {
            $result = $captchaStatus;
            if (isset($captchaStatus['error'])) {
                $result = $captchaStatus;
                $flag   = false;
            }
        }
        if ($this->getRequest()->isPost() && $flag) {
            $login = $this->getRequest()->getPost();
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $flag = $session->login($login['username'], $login['password']);
                    if ($flag) {
                        $result['success'] = true;
                        $result['message'] = $this->__('Login successfully. Please wait ...');
                    } else {
                        $result['error']   = true;
                        $result['message'] = $this->__('Login and password are required.');
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value   = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                    $result['error']   = true;
                    $result['message'] = $message;
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                //                $session->addError($this->__('Login and password are required.'));
                $result['error']   = true;
                $result['message'] = $this->__('Login and password are required.');
            }

        }
        $session->setResultCaptcha(null);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Process forgot ajax action when click Forgot Button
     *
     * @throws Exception
     * @throws Zend_Validate_Exception
     */
    public function forgotAction()
    {
        $flag          = true;
        $result        = array(
            'success' => false,
            'message' => array()
        );
        $session       = $this->_getSession();
        $captchaStatus = $session->getResultCaptcha();
        if ($captchaStatus) {
            $result = $captchaStatus;
            if (isset($captchaStatus['error'])) {
                $result = $captchaStatus;
                $flag   = false;
            }
        }
        if ($flag) {
            $email = (string)$this->getRequest()->getPost('email');
            if ($email) {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    $this->_getSession()->setForgottenEmail($email);
                    $result['error']   = true;
                    $result['message'] = $this->__('Invalid email address.');
                } else {
                    $customer = $this->_getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email);
                    if ($customer->getId()) {
                        try {
                            $newResetPasswordLinkToken = $this->_getHelper('customer')->generateResetPasswordLinkToken();
                            $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                            $customer->sendPasswordResetConfirmationEmail();
                        } catch (Exception $exception) {
                            $result['error']   = true;
                            $result['message'] = $exception->getMessage();
                            $flag              = false;
                        }
                        if ($flag) {
                            $result['success'] = true;
                            $result['message'] = $this->__('If there is an account associated with the email you will receive an email with a link to reset your password.');
                        }
                    } else {
                        $result['success'] = true;
                        $result['message'] = $this->__('If there is an account associated with the email you will receive an email with a link to reset your password.');
                    }
                }
            } else {
                $result['error']   = true;
                $result['message'] = $this->__('Please enter your email.');
            }
        }
        $session->setResultCaptcha(null);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Process create ajax action when click Create Button
     */
    public function createAction()
    {
        $flag          = true;
        $result        = array(
            'success' => false,
            'message' => array()
        );
        $session       = $this->_getSession();
        $captchaStatus = $session->getResultCaptcha();
        if ($session->isLoggedIn()) {
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if (!$this->getRequest()->isPost()) {
            $result['error']     = true;
            $result['message'][] = $this->__('Error Data.');
        } elseif ($captchaStatus) {
            $result = $captchaStatus;
            if (isset($captchaStatus['error'])) {
                $result = $captchaStatus;
                $flag   = false;
            }
        }
        if ($flag) {
            $customer = $this->_getCustomer();
            try {
                $errors = $this->_getCustomerErrors($customer);
                if (empty($errors)) {
                    $customer->cleanPasswordsValidationData();
                    $customer->save();
                    if ($customer->isConfirmationRequired()) {
                        /** @var $app Mage_Core_Model_App */
                        $app = $this->_getApp();
                        /** @var $store  Mage_Core_Model_Store */
                        $store = $app->getStore();
                        $customer->sendNewAccountEmail(
                            'confirmation',
                            $session->getBeforeAuthUrl(),
                            $store->getId()
                        );
                        $customerHelper      = $this->_getHelper('customer');
                        $result['success']   = false;
                        $result['message'][] = $this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',
                            $customerHelper->getEmailConfirmationUrl($customer->getEmail()));
                    } else {
                        $session->setCustomerAsLoggedIn($customer);
                        $result['success']   = true;
                        $result['message'][] = $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName());
                    }
                } else {
                    $result['error']     = true;
                    $result['message'][] = $errors[1];
                }
            } catch (Mage_Core_Exception $e) {
                $result['error']     = true;
                $result['message'][] = $e->getMessage();
                $session->setCustomerFormData($this->getRequest()->getPost());
            } catch (Exception $e) {
                $session->setCustomerFormData($this->getRequest()->getPost());
                $result['error']     = true;
                $result['message'][] = $this->__('Cannot save the customer.');
            }
        }
        $session->setResultCaptcha(null);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _getHelper($path)
    {
        return Mage::helper($path);
    }

    public function _getModel($path, $arguments = array())
    {
        return Mage::getModel($path, $arguments);
    }

    protected function _getCustomer()
    {
        $customer = $this->_getFromRegistry('current_customer');
        if (!$customer) {
            $customer = $this->_getModel('customer/customer')->setId(null);
        }
        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /**
         * Initialize customer group id
         */
        $customer->getGroupId();

        return $customer;
    }

    protected function _getFromRegistry($path)
    {
        return Mage::registry($path);
    }

    protected function _getCustomerErrors($customer)
    {
        $errors  = array();
        $request = $this->getRequest();
        if ($request->getPost('create_address')) {
            $errors = $this->_getErrorsOnCustomerAddress($customer);
        }
        $customerForm   = $this->_getCustomerForm($customer);
        $customerData   = $customerForm->extractData($request);
        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== true) {
            $errors = array_merge($customerErrors, $errors);
        } else {
            $customerForm->compactData($customerData);
            $customer->setPassword($request->getPost('password'));
            $customer->setPasswordConfirmation($request->getPost('confirmation'));
            $customerErrors = $customer->validate();
            if (is_array($customerErrors)) {
                $errors = array_merge($customerErrors, $errors);
            }
        }

        return $errors;
    }

    protected function _getErrorsOnCustomerAddress($customer)
    {
        $errors = array();
        /* @var $address Mage_Customer_Model_Address */
        $address = $this->_getModel('customer/address');
        /* @var $addressForm Mage_Customer_Model_Form */
        $addressForm = $this->_getModel('customer/form');
        $addressForm->setFormCode('customer_register_address')
            ->setEntity($address);

        $addressData   = $addressForm->extractData($this->getRequest(), 'address', false);
        $addressErrors = $addressForm->validateData($addressData);
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }
        $address->setId(null)
            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
        $addressForm->compactData($addressData);
        $customer->addAddress($address);

        $addressErrors = $address->validate();
        if (is_array($addressErrors)) {
            $errors = array_merge($errors, $addressErrors);
        }

        return $errors;
    }

    protected function _getCustomerForm($customer)
    {
        /* @var $customerForm Mage_Customer_Model_Form */
        $customerForm = $this->_getModel('customer/form');
        $customerForm->setFormCode('customer_account_create');
        $customerForm->setEntity($customer);

        return $customerForm;
    }

    protected function _getUrl($url, $params = array())
    {
        return Mage::getUrl($url, $params);
    }

    protected function _getApp()
    {
        return Mage::app();
    }
}