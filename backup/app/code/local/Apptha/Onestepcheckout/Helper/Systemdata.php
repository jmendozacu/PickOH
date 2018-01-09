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
 * This class contains Data manipulation functions
 *
 */
class Apptha_Onestepcheckout_Helper_Systemdata extends Mage_Core_Helper_Abstract {

    /**
     * Get customer session Model
     * @return object
     */
    public function getOscCustomerSessionModel(){
        return  Mage::getSingleton('customer/session');
    }
    /**
     * Get core session Model
     * @return object
     */
    public function getOscCheckoutSessionModel(){
        return  Mage::getSingleton ( 'checkout/session' );
    }
    /**
     * Function to get onestepcheckout helper
     * @return object
     */
    public function getOneStepCheckoutHelper(){
        return Mage::helper ( 'onestepcheckout/checkout' );
    }

    /**
     * Function to get osc helper
     * @return object
     */
    public function getOscHelper(){
        return Mage::helper ( 'onestepcheckout' );
    }
    /**
     *Function to get onepage model object
     *@return object
     */
    public function getOnepage() {
        return Mage::getSingleton ( 'checkout/type_onepage' );
    }
}