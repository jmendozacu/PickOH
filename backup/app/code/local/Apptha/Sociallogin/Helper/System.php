<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Marketplace
 * @version     1.9.2
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2016 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */

/**
 * This file is used to get twitter url and license key functionality
 *
 * In this class, get twitter url and license key operations are included.
 */
class Apptha_Sociallogin_Helper_System extends Mage_Core_Helper_Abstract {
    /**
     *
     * Function to get default billing
     * @param unknown $addressErrors
     * @param unknown $defaultBilling
     * @param unknown $defaultShipping
     * @param unknown $addressData
     * @param unknown $address
     * @param unknown $addressForm
     * @param unknown $customer
     * @return errors
     */
    public function checkDefaultBilling($addressErrors,$defaultBilling,$defaultShipping,$addressData,$address,$addressForm,$customer){
        if ($addressErrors === true) {
            $address->setId ( null )->setIsDefaultBilling ( $defaultBilling)->setIsDefaultShipping ( $defaultShipping );
            $addressForm->compactData ( $addressData );
            $customer->addAddress ( $address );
            $addressErrors = $address->validate ();
            $errors=Mage::helper('sociallogin')->checkAddressErrors($errors,$addressErrors);
        } else {
            $errors = array_merge ( $errors, $addressErrors );
        }
        return $errors;
    }

    /**
     * Function to get Customer helper
     * @return object
     */

    public function getCustomerHelper(){
        return  Mage::helper ( 'customer' );
    }
    /**
     * Function to get Secure Url
     * @return string
     */
    public function getSecureUrl(){
       return  Mage::getUrl('/index',array('_secure' => true));
    }
    /**
     * Function to get Customer Model
     * @return object
     */
    public function getCustomerModel(){
        return Mage::getModel('customer/customer');
    }
    /**
     * Function to get Seller Profile
     * @return object
     */
    public function getSellerProfileModel(){
        return Mage::getModel ( 'marketplace/sellerprofile' );
    }
    /**
     * Function to get Core Session
     * @return object
     */
    public function getCoreSessionObject(){
        return Mage::getSingleton ( 'core/session' );
    }
}