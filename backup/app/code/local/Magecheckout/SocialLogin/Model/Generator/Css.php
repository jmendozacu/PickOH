<?php

class Magecheckout_SocialLogin_Model_Generator_Css extends Mage_Core_Model_Abstract
{
    public function __construct() { parent::__construct(); }

    public function generateCss($websiteCode, $storeCode, $section)
    {
        if ($websiteCode) {
            if ($storeCode) {
                $this->_generateStoreCss($storeCode, $section);
            } else {
                $this->_generateWebsiteCss($websiteCode, $section);
            }
        } else {
            $stores = Mage::app()->getWebsites(false, true);
            foreach ($stores as $store) {
                $this->_generateWebsiteCss($store, $section);
            }
        }
    }

    protected function _generateWebsiteCss($websiteCode, $section)
    {
        $websites = Mage::app()->getWebsite($websiteCode);
        foreach ($websites->getStoreCodes() as $store) {
            $this->_generateStoreCss($store, $section);
        }
    }

    protected function _generateStoreCss($storeCode, $section)
    {
        $_store = Mage::app()->getStore($storeCode);
        if (!$_store->getIsActive()) return;
        if (Mage::helper('sociallogin')->isDisabledCustomCss($_store->getId())) {
            Mage::getModel('core/config')->saveConfig('sociallogin/general/disable_custom_css', 0, 'stores', $_store->getId());
        }
        $store       = '_' . $storeCode;
        $cssFile     = $section . $store . '.css';
        $cssFileDir  = Mage::helper('sociallogin/generator_css')->getGeneratedCssDir() . $cssFile;
        $cssTemplate = Mage::helper('sociallogin/generator_css')->getTemplatePath() . $section . '.phtml';
        Mage::register('sociallogin_generator_css_store', $storeCode);
        try {
            $cssGenerated = Mage::app()->getLayout()->createBlock('sociallogin/generator_css')
                ->setData('area', 'frontend')
                ->setTemplate($cssTemplate)
                ->setStoreId(Mage::app()->getStore($storeCode)->getId())
                ->toHtml();
            if (empty($cssGenerated)) {
                throw new Exception(Mage::helper('sociallogin')->__("Template file is empty or doesn\'t exist: %s", $cssTemplate));
            }
            $varienFile = new Varien_Io_File();
            $varienFile->setAllowCreateFolders(true);
            $varienFile->open(array('path' => Mage::helper('sociallogin/generator_css')->getGeneratedCssDir()));
            $varienFile->streamOpen($cssFileDir, 'w+', 0777);
            $varienFile->streamLock(true);
            $varienFile->streamWrite($cssGenerated);
            $varienFile->streamUnlock();
            $varienFile->streamClose();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sociallogin')->__('Failed generating CSS file: %s in %s', $cssFile, Mage::helper('sociallogin/generator_css')->getGeneratedCssDir()) . '<br/>Message: ' . $e->getMessage());
            Mage::logException($e);
        }
        Mage::app()->getCacheInstance()->flush();
        Mage::unregister('sociallogin_generator_css_store');
    }
}