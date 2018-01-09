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
 * Function written in this file are globally accessed
 */
class Apptha_Marketplace_Helper_System extends Mage_Core_Helper_Abstract {

    /**
     * Function to get Transaction model
     * @return object
     */

    public function getTransactionModel(){
       return  Mage::getModel('marketplace/transaction');
    }
    /**
     * Function to get Marketplace Helper
     * @return object
     */
    public function getMarketplaceHelper(){
        return Mage::helper ( 'marketplace' );
    }
    /**
     * Function to get Marketplace general Helper
     * @return object
     */
    public function getMarketplaceGeneralHelper(){
        return Mage::helper ( 'marketplace/general' );
    }

    /**
     * Function to get Marketplace outofstock Helper
     * @return object
     */
    public function getMarketplaceOutOfStockHelper(){
        return Mage::helper ( 'marketplace/outofstock' );
    }

    /**
     * Function to get Marketplace outofstock Helper
     * @return object
     */
    public function getCustomerAddressHelper(){
        return Mage::helper ( 'customer/address' );
    }
    /**
     * Function to define Entity Id
     * @return string
     */
    public function getGridEntityId(){
        return 'entity_id';
    }

    /**
     * Function to get Category Model
     * @return object
     */
    public function getCategoryModel(){
        return Mage::getModel ( 'catalog/category' );
    }
    /**
     * Function to get Commission Model
     * @return object
     */
    public function getCommissionModel(){
        return Mage::getModel ( 'marketplace/commission' );
    }
    /**
     * Function to get Product model
     * @return object
     *
     */
    public function getProductModel(){
        return Mage::getModel ( 'catalog/product' );
    }
    /**
     * Function to get adminhtml session
     * @return Object
     */
    public function getAdminhtmlSessionModel(){
        return Mage::getSingleton ( 'adminhtml/session' );
    }
    /***
     * Function to get Seller Profile Model
     * @return object
     */
    public function getSellerProfileModel(){
        return Mage::getModel ( 'marketplace/sellerprofile' );
    }
    /**
     * Function to get Customer Model
     * @return object
     */
    public function getCustomerModel(){
        return Mage::getModel ( 'customer/customer' );
    }
    /**
     * Function to get core email template model
     * @return object
     */
    public function getCoreEmailTemplateModel(){
        return Mage::getModel ( 'core/email_template' );
    }
    /**
     * Function to get Mp download model
     * @return object
     */
    public function getMarketplaceDownloadModel(){
        return Mage::getModel ( 'marketplace/download' );
    }

    /**
     * Function to get Mp download model
     * @return object
     */
    public function getMarketplaceSellerModel(){
        return Mage::getModel ( 'marketplace/seller' );
    }
    /**
     * Function to get Mp product model
     * @return object
     */
    public function getMarketplaceProductModel(){
        return Mage::getModel ( 'marketplace/product' );
    }
    /**
     * Get customer session Model
     * @return object
     */
    public function getCustomerSessionModel(){
        return  Mage::getSingleton('customer/session');
    }
    /**
     * Get core session Model
     * @return object
     */
    public function getCoreSessionModel(){
        return  Mage::getSingleton ( 'core/session' );
    }
    /**
     * Check whether VAT ID validation is enabled
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return bool
     */
    public function _isVatValidationEnabled($store = null) {
        return $this->getCustomerAddressHelper ()->isVatValidationEnabled ( $store );
    }
    /**
     * Function to get observer model
     * @return object
     */
    public function getObserverModel(){
        return Mage::getModel('marketplace/observer');
    }
}