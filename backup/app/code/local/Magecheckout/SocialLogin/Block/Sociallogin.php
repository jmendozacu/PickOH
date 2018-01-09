<?php

class Magecheckout_SocialLogin_Block_Sociallogin extends Mage_Core_Block_Template
{
    /**
     * Return null if module is not enabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Check module is enabled
     *
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->helperData()->isEnabled();
    }

    /**
     * get social login helper data
     *
     * @return Magecheckout_SocialLogin_Helper_Data
     */
    public function helperData()
    {
        return Mage::helper('sociallogin');
    }

    /**
     * get is secure url
     *
     * @return mixed
     */
    public function isSecure()
    {
        return $this->helperData()->isSecure();
    }

    /**
     * get Social Login Form Url
     *
     * @return string
     */
    public function getFormLoginUrl()
    {
        return $this->getUrl('sociallogin/popup/login', ['_secure' => $this->isSecure()]);
    }

    /**
     *  get Social Login Form Create Url
     *
     * @return string
     */
    public function getCreateFormUrl()
    {
        return $this->getUrl('sociallogin/popup/create', ['_secure' => $this->isSecure()]);
    }

    /**
     * get Social Login Forgot Url
     */
    public function getForgotFormUrl()
    {
        return $this->getUrl('sociallogin/popup/forgot', ['_secure' => $this->isSecure()]);
    }

    public function getPopupEffect()
    {
        return $this->helperData()->getPopupEffect();
    }


    /**
     * get Header Login Link Selector
     *
     * @return mixed
     */
    public function getHeaderLinkSelector()
    {
        return $this->helperData()->getHeaderLinkSelector();
    }

    /**
     * get Custom Login Link Selector
     *
     * @return mixed
     */
    public function getCustomLinkSelector()
    {
        $customLinks = explode(',', $this->helperData()->getCustomLinkSelector());

        return $customLinks;
    }

}
