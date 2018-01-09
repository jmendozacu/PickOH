<?php

class Magecheckout_SocialLogin_Model_Observer
{
    /** observer when customer click to login button
     *
     * @param $observer
     * @return $this
     */
    public function checkUserLogin($observer)
    {
        if (!$this->_isEnabled()) {
            return $this;
        }
        $formId       = 'user_login';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $controller   = $observer->getControllerAction();
        $loginParams  = $controller->getRequest()->getPost();
        $login        = isset($loginParams['username']) ? $loginParams['username'] : null;
        $result       = array();
        if ($captchaModel->isRequired($login)) {
            $word = $this->_getCaptchaString($controller->getRequest(), $formId);
            if (!$captchaModel->isCorrect($word)) {
                $result['error']   = true;
                $result['message'] = Mage::helper('sociallogin')->__('Incorrect CAPTCHA.');
            }
            $captchaModel->generate();
            $result['imgSrc'] = $captchaModel->getImgSrc();
            Mage::getSingleton('customer/session')->setResultCaptcha($result);
            $captchaModel->logAttempt($login);
        }


        return $this;
    }

    /**
     * Observer when customer click create button
     *
     * @param $observer
     * @return $this
     */
    public function checkUserCreate($observer)
    {
        if (!$this->_isEnabled()) {
            return $this;
        }
        $formId       = 'user_create';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                $result['error']   = true;
                $result['message'] = Mage::helper('sociallogin')->__('Incorrect CAPTCHA.');
            }
            $captchaModel->generate();
            $result['imgSrc'] = $captchaModel->getImgSrc();
            Mage::getSingleton('customer/session')->setResultCaptcha($result);
        }

        return $this;

    }

    /**
     * Observer when customer click forgot button
     *
     * @param $observer
     * @return $this
     */
    public function checkUserForgot($observer)
    {
        if (!$this->_isEnabled()) {
            return $this;
        }
        $formId       = 'user_forgotpassword';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                $result['error']   = true;
                $result['message'] = Mage::helper('sociallogin')->__('Incorrect CAPTCHA.');
            }
            $captchaModel->generate();
            $result['imgSrc'] = $captchaModel->getImgSrc();
            Mage::getSingleton('customer/session')->setResultCaptcha($result);
        }

        return $this;
    }

    /**
     * Observer Customer after save event
     *
     * @param $observer
     * @return $this
     */
    public function customerAccountEditPost($observer)
    {
        if (!$this->_isEnabled()) {
            return $this;
        }
        $customer = $observer->getEvent()->getCustomer();
        if (($customer instanceof Mage_Customer_Model_Customer)) {
            $customerId = $customer->getId();
            $isError    = $this->_getSession()->getMessages()->getErrors();
            if (!count($isError) && $customerId) {
                $author = Mage::getModel('sociallogin/author')->load($customerId, 'customer_id');
                $helper = $this->getSocialHelper($author->getType());
                if (!$helper->sendPassword()) {
                    return $this;
                }
                if ($author && $author->getId() && !$author->getIsSendPasswordEmail()) {
                    if ($this->checkConditionSendPassword($author, $customer->getEmail())) {
                        $customer->sendPasswordReminderEmail();
                        try {
                            $author->setData('is_send_password_email', 1);
                            $author->save();
                        } catch (Exception $e) {
                            $this->_getSession()->addError($e);
                        }
                    }
                }
            }
        }

        return $this;
    }

    protected function checkConditionSendPassword($author, $email)
    {
        $type = $author->getType();
        if (
            ($type == 'facebook' && strpos($email, 'facebook') === false) ||
            ($type == 'twitter' && strpos($email, 'twitter') === false) ||
            ($type == 'instagram' && strpos($email, 'instagram') === false)
        ) {
            return true;
        }

        return false;
    }

    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);

        return $captchaParams[$formId];
    }

    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Check Module is enabled
     *
     * @param null $store
     * @return mixed
     */
    protected function _isEnabled($store = null)
    {
        return Mage::helper('sociallogin')->isEnabled($store);
    }

    /**
     * get Helper by type
     *
     * @param $type
     * @return mixed
     */
    public function getSocialHelper($type)
    {
        return Mage::helper('socillogin/' . $type);
    }
}