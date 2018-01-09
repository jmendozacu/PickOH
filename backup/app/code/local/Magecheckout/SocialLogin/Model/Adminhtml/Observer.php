<?php

class Magecheckout_SocialLogin_Model_Adminhtml_Observer
{
    /**
     * Generate css file when save config admin
     * @return $this
     */
    public function adminhtmlSystemConfigSave()
    {
        $section = Mage::app()->getRequest()->getParam('section');
        if ($section == 'sociallogin') {
            $websiteCode   = Mage::app()->getRequest()->getParam('website');
            $storeCode     = Mage::app()->getRequest()->getParam('store');
            $css_generator = Mage::getSingleton('sociallogin/generator_css');
            $css_generator->generateCss($websiteCode, $storeCode, 'design');
        }

        return $this;
    }

}