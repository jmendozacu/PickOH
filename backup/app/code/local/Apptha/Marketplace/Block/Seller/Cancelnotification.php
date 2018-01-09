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
 * @version     1.9.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2016 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
/**
 * Seller information
 * This class used to display the seller information
 * Display the seller products
 * Display review form
 * Display the Ratings
 * Display the reviews those have been already submitted by users
 */
class Apptha_Marketplace_Block_Seller_Cancelnotification extends Mage_Core_Block_Template {
    /**
     * Function to load layout
     *
     * @return array
     */
   public function getOrderInformation($orderId){
       return Mage::getModel('sales/order')->load($orderId);
   }
   public function getSellerInformation($sellerId){
       return Mage::getModel('customer/customer')->load($sellerId);
   }
   public function getProductInformation($productId){
       return Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );

   }

}
