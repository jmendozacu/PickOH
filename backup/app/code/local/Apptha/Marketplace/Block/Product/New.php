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
 * New product
 * This Class is used for add new product functionality
 */
class Apptha_Marketplace_Block_Product_New extends Mage_Core_Block_Template {

    /**
     * Initilize layout and set page title
     *
     * Return the page title
     *
     * @return varchar
     */
    protected function _prepareLayout() {
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper ( 'marketplace' )->__ ( 'New Product' ) );
        return parent::_prepareLayout ();
    }

    /**
     * New product add action
     *
     * Return new product add action url
     *
     * @return string
     */
    public function addProductAction() {
        return Mage::getUrl ( 'marketplace/product/newpost' );
    }

    /**
     * Getting website id
     *
     * Return the product website id
     *
     * @return int
     */
    public function getWebsiteId() {
        return Mage::app ()->getStore ( true )->getWebsite ()->getId ();
    }

    /**
     * Getting store id
     *
     * Return product store id
     *
     * @return int
     */
    public function getStoreId() {
        return Mage::app ()->getStore ()->getId ();
    }

    /**
     * Getting attributeset id
     *
     * Return the product attribute set id
     *
     * @return int
     */
    public function getAttributeSetId() {
        return Mage::getModel ( 'catalog/product' )->getResource ()->getEntityType ()->getDefaultAttributeSetId ();
    }

    /**
     * Sort the categories in alphabatical order
     */
    public function alphabaticalOrder($categories) {
        $array = array ();
        $customerName = array ();
        foreach ( $categories as $category ) {
            $catagoryId = $category->getId ();
            if ($category->hasChildren ()) {
                $catagoryId = $category->getId () . 'sub';
            }
            $customerName [$catagoryId] = $category->getName ();
        }
        asort ( $customerName );

        return $array = $this->show_categories_tree ( $customerName );
    }
    /**
     * Getting store categories list
     *
     * Passed category information as array
     *
     * @param array $categories
     *            Return the category tree array
     * @return array
     */
    public function show_categories_tree($customerName) {
        $array = '<ul class="category_ul">';
        foreach ( $customerName as $key => $catname ) {

            $catagory = Mage::helper ( 'marketplace/common' )->getCategoryData ( $key );
            $count = $catagory->getProductCount ();
            if (strstr ( $key, 'sub' )) {
                $key = str_replace ( 'sub', '', $key );
                $array .= '<li class="level-top  parent" id="' . $key . '"><a href="javascript:void(0);"><span class="end-plus" id="' . $key . '"></span></a><span class="last-collapse"><input id="cat' . $key . '" type="checkbox" name="category_ids[]" value="' . $key . '"><label for="cat' . $key . '">' . $catname . '<span>(' . $count . ')</span>' . '</label></span>';
            } else {
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="empty_space"></span></a><input id="cat' . $key . '" type="checkbox" name="category_ids[]" value="' . $key . '"><label for="cat' . $key . '">' . $catname . '<span>(' . $count . ')</span>' . '</label>';
            }
        }
        $array .= '</li>';
        return $array . '</ul>';
    }
    
    /**
     * Function to get attribute collection
     * @param int $groupid
     * @return object
     * 
     */
    public function getAttributeCollection($groupId){
        
       return  Mage::getResourceModel('catalog/product_attribute_collection')->setAttributeGroupFilter($groupId)->addVisibleFilter()->checkConfigurableProducts()->load();
    }
    
    /**
     * Function to get attribute collection
     * @param int $attributeSetId
     * @return object
     *
     */
    public function setAttributeCollection($attributeSetId){
    
        return  Mage::getResourceModel('eav/entity_attribute_group_collection')->setAttributeSetFilter($attributeSetId)->setSortOrder()->load();
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
     * Function to get categories
     * @param rootid
     * @return object
     *
     */
    public function categoryCollection($rootcatId){
    
        return  Mage::getModel('catalog/category')->getCategories($rootcatId);
    }
    
    /**
     * Function to get shipping attribute
     * @param $shippingtype
     * @return object
     *
     */
    public function getShippingAttribute($shippingtypeIdVar){
    
        return  Mage::getModel ( 'eav/config' )->getAttribute ( 'catalog_product', $shippingtypeIdVar );
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
     * Function to get resource type id
     * 
     * @return object
     *
     */
    public function getResourceTypeId(){
    
        return  Mage::getModel('catalog/product')->getResource()->getTypeId();
    }
    /**
     * Function to get vacation details
     * @param int $entityType
     * @return object
     *
     */
    public function getEavCollection($entityType){
    
        return  Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityType)->setOrder('attribute_set_id','asc');
    }
}
