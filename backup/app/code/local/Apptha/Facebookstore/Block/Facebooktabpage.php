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
class Apptha_Facebookstore_Block_Facebooktabpage extends Mage_Core_Block_Template{
    /**
     * Function to load layout
     *
     * @return object
     *
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        $catId = $this->getRequest ()->getParam ( 'catid' );
        $searchTerm = $this->getRequest ()->getParam ( 'searchTerm' );
        if (is_numeric ( $catId ) || $catId == '') {
            $product_collection = $this->getProductdisplay ( $catId, $searchTerm );
            $this->setCollection ( $product_collection );
            $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $product_collection );
            $this->setChild ( 'pager', $pager );
        }
        return $this;
    }
    /**
     * Function to load pagination
     *
     * @return pager
     *
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    /**
     * Function to display products
     *
     * @return collection
     *
     */
    public function getProductdisplay($catId, $searchTerm) {
        $storeId = Mage::app ()->getStore ()->getId ();
        $sellerId = $this->getRequest ()->getParam ( 'sellerid' );
        if ($searchTerm != '') {
            $filter_a = array (
                    'like' => $searchTerm . '%'
            );
            $category = Mage::getModel ( 'catalog/category' )->load ( $catId );

            if (! empty ( $sellerId )) {
                $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addStoreFilter ( $storeId )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'seller_id', $sellerId );
            } else {
                $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addStoreFilter ( $storeId )->addAttributeToSelect ( '*' );
            }
            $collection = $collection->addCategoryFilter ( $category )->addFieldToFilter ( 'name', array (
                    $filter_a
            ) )->setPageSize ( 1, 9 );
        }

        elseif ($catId != '' && is_numeric ( $catId )) {
            $category = Mage::getModel ( 'catalog/category' )->load ( $catId );
            if (! empty ( $sellerId )) {
                $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addStoreFilter ( $storeId )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'seller_id', $sellerId )->addCategoryFilter ( $category )->setPageSize ( 1, 9 );
            } else {
                $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addStoreFilter ( $storeId )->addAttributeToSelect ( '*' )->addCategoryFilter ( $category )->setPageSize ( 1, 9 );
            }
        }

        else {
            $category = Mage::getModel ( 'catalog/category' )->load ( $catId );
            if (! empty ( $sellerId )) {
                $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addStoreFilter ( $storeId )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'seller_id', $sellerId );
            } else {
                $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addStoreFilter ( $storeId )->addAttributeToSelect ( '*' );
            }

            $collection = $collection->addCategoryFilter ( $category )->setPageSize ( 1, 9 );
        }
        $collection->addAttributeToFilter ( 'status', array (
                'eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED
        ) );
        $visibility = array (
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
        );
        $collection->addAttributeToFilter ( 'visibility', $visibility );
        return $collection;
    }
    /**
     * Function to get Seller object
     * @param $sellerid
     * @return object
     */
     public function getSellerObject($sellerId){
         return Mage::getModel ( 'marketplace/facebookstore' )->load ( $sellerId, 'seller_id' );
     }
     /**
      * Function to get category object
      * @param $categoryId
      * @return object
      */
     public function getCategoryObject($categoryId){
         return Mage::getModel('catalog/category')->load($categoryId);
     }
}