<?php
/**
 * Magecheckout_SocialLogin extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Magecheckout
 * @package        Magecheckout_SocialLogin
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */

/**
 * SocialLogin default helper
 *
 * @category    Magecheckout
 * @package     Magecheckout_SocialLogin
 * @author      Ultimate Module Creator
 */
class Magecheckout_SocialLogin_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_GENERAL_ENABLED = 'sociallogin/general/is_enabled';
    const XML_PATH_GENERAL = 'sociallogin/general/';
    const XML_PATH_GENERAL_POPUP_LEFT = 'sociallogin/general/left';
    const XML_PATH_GENERAL_STYLE_MANAGEMENT = 'sociallogin/general/style_management';
    const XML_PATH_CAPTCHA_ENABLE = 'sociallogin/captcha/is_enabled';
    const XML_PATH_SECURE_IN_FRONTEND = 'web/secure/use_in_frontend';

    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_ENABLED, $storeId);

    }

    public function isCaptchaEnabled($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CAPTCHA_ENABLE, $storeId);

    }

    function getGeneralConfig($code, $store = null)
    {
        if (!$store)
            $store = Mage::app()->getStore()->getId();

        return Mage::getStoreConfig(self::XML_PATH_GENERAL . $code, $store);
    }

    /**
     * get Header Login Link Element Id
     *
     * @param null $store
     * @return mixed
     */
    public function getHeaderLinkSelector($store = null)
    {
        return $this->getGeneralConfig('header_link_selector', $store);
    }

    /**
     * get Custom Login Link Element Id
     *
     * @param null $store
     * @return mixed
     */
    public function getCustomLinkSelector($store = null)
    {
        return $this->getGeneralConfig('custom_link_selector', $store);
    }

    public function getPopupEffect($storeId = null)
    {
        return $this->getGeneralConfig('popup_effect', $storeId);
    }

    public function getStyleManagement($storeId = null)
    {
        $style = $this->getGeneralConfig('style_color', $storeId);
        if ($style == 'custom') {
            return '#' . $this->getCustomColor($storeId);
        }

        return $style;
    }

    public function getCustomColor($storeId = null)
    {
        return $this->getGeneralConfig('style_custom', $storeId);
    }

    public function getCustomCss($storeId = null)
    {
        return $this->getGeneralConfig('custom_css', $storeId);
    }

    public function isDisabledCustomCss($storeId = null)
    {
        return $this->getGeneralConfig('disable_custom_css', $storeId);
    }

    public function createCustomerAccount($data, $website_id, $store_id)
    {
        $customer = Mage::getModel('customer/customer')->setId(null);
        $customer->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setWebsiteId($website_id)
            ->setStoreId($store_id)
            ->save();
        $newPassword = $customer->generatePassword();
        $customer->setPassword($newPassword);
        try {
            $customer->save();
            Mage::dispatchEvent('sociallogin_customer_register_success', array(
                'customer' => $customer
            ));
        } catch (Exception $e) {

        }

        return $customer;
    }

    public function getCustomerByEmail($email, $websiteId)
    {
        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId($websiteId)
            ->loadByEmail($email);

        return $customer;
    }

    public function isSecure($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_SECURE_IN_FRONTEND, $store);
    }

}
