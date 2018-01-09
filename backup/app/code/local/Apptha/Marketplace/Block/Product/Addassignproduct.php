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
 * Manage seller products functionality
 */
class Apptha_Marketplace_Block_Product_Addassignproduct extends Mage_Core_Block_Template {
    
    /**
     * Collection for manage products
     *
     * @return \Apptha_Marketplace_Block_Product_Manage
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        return $this;
    }
    
    /**
     * Function to get add assign product url
     *
     * Return the add assign product url
     *
     * @return string
     */
    public function saveAssignProductUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/saveassignproduct' );
    }
    
    /**
     * Function to get product Qty
     * @param int $productId
     * @return object
     *
     */
    public function getProductQty($productId){
    
        return  Mage::getModel ( 'cataloginventory/stock_item' )->loadByProduct ( $productId )->getQty ();
    }
    
    /**
     * Function to get product instock
     * @param int $productId
     * @return object
     *
     */
    public function getProductIsInStock($productId){
    
        return Mage::getModel ( 'cataloginventory/stock_item' )->loadByProduct ( $productId )->getIsInStock ();
    }
    
    /**
     * Function to get vacation details
     * @param int $sellerId
     * @return object
     *
     */
    public function getVacationDetails($sellerIdValue){
    
        return  Mage::getModel ( 'marketplace/vacationmode' )->load ( $sellerIdValue, 'seller_id' );
    }
    
    /**
     * Function to get shipping attribute
     * @param $shippingtype
     * @return object
     *
     */
    public function getAttributeData($attribute){
    
        return  Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attribute );
    }
    /**
     * Function to get country collection
     * @return object
     *
     */
    public function countryCollection(){
    
        return  Mage::getModel ( 'directory/country' )->getCollection ();
    }
    
    /**
     * Function to get downloadable link collection
     * @return object
     *
     */
    public function downloadableLinkCollection($product){
    
        return  Mage::getModel ( 'downloadable/link' )->getCollection ()->addProductToFilter ( $product->getId () )->addTitleToResult ( $product->getStoreId () )->addPriceToResult ( $product->getStore ()->getWebsiteId () );
    }
    
    /**
     * Function to get downloadable link collection
     * @return object
     *
     */
    public function downloadableSampleCollection($product){
    
        return  Mage::getModel ( 'downloadable/link' )->getCollection ()->addProductToFilter ( $product->getId () )->addTitleToResult ( $product->getStoreId () )->addPriceToResult ( $product->getStore ()->getWebsiteId () );
    }
    /**
     * Function to get used product data
     * @return object
     *
     */
    public function getUsedProductsData($product){
    
        return  Mage::getModel ( 'catalog/product_type_configurable' )->getUsedProducts ( null, $product );
    }
}