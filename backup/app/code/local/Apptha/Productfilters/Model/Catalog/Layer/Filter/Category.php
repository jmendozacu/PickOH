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
class Apptha_Productfilters_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category{
    /**
     * class construct
     * @return void
     *
     *
     */
    public function __construct() {
        parent::__construct ();
        $this->_requestVar = 'cat';
    }
    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData() {
        if (! Mage::helper ( 'productfilters' )->isEnabled ()) {
            return parent::_getItemsData ();
        }

        $key = $this->getLayer ()->getStateKey () . '_SUBCATEGORIES';
        $data = $this->getLayer ()->getAggregator ()->getCacheData ( $key );

        if ($data === null) {
            $categoty = $this->getCategory ();
            /** @var $categoty Mage_Catalog_Model_Categeory */
            $categories = $categoty->getChildrenCategories ();

            $this->getLayer ()->getProductCollection ()->addCountToCategories ( $categories );

            $data = array ();
            foreach ( $categories as $category ) {
                if ($category->getIsActive () && $category->getProductCount ()) {
                    $urlKey = $category->getUrlKey ();
                    if (empty ( $urlKey )) {
                        $urlKey = $category->getId ();
                    }

                    $data [] = array (
                            'label' => Mage::helper ( 'core' )->htmlEscape ( $category->getName () ),
                            'value' => $urlKey,
                            'count' => $category->getProductCount ()
                    );
                }
            }
            $tags = $this->getLayer ()->getStateTags ();
            $this->getLayer ()->getAggregator ()->saveCacheData ( $data, $key, $tags );
        }
        return $data;
    }

    /**
     * Apply category filter to layer
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Core_Block_Abstract $filterBlock
     * @return Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        $filter = $request->getParam ( $this->getRequestVar () );
        if (! Mage::helper ( 'productfilters' )->isEnabled ()|| (! $filter)) {
            return parent::apply ( $request, $filterBlock );
        }
        $this->_appliedCategory = Mage::getModel ( 'catalog/category' )->setStoreId ( Mage::app ()->getStore ()->getId () )->loadByAttribute ( 'url_key', $filter );
        if (! ($this->_appliedCategory instanceof Mage_Catalog_Model_Category)) {
            return parent::apply ( $request, $filterBlock );
        }
        $this->_categoryId = $this->_appliedCategory->getId ();
        Mage::register ( 'current_category_filter', $this->getCategory (), true );
        if ($this->_isValidCategory ( $this->_appliedCategory )) {
            $this->getLayer ()->getProductCollection ()->addCategoryFilter ( $this->_appliedCategory );

            $this->getLayer ()->getState ()->addFilter ( $this->_createItem ( $this->_appliedCategory->getName (), $filter ) );
        }
        return $this;
    }
}