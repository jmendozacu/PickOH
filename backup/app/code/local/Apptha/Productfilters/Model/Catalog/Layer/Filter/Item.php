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
class Apptha_Productfilters_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item{
    protected $_helper;
    /**
     * Function to get helper
     * @return object
     *
     */
    protected function _helper() {
        if ($this->_helper === null) {
            $this->_helper = Mage::helper ( 'productfilters' );
        }
        return $this->_helper;
    }


    /**
     * Check if current filter is selected
     *
     * @return boolean
     */
    public function isSelected() {
        $values = $this->getFilter ()->getValues ();
        if (in_array ( $this->getValue (), $values )) {
            return true;
        }
        return false;
    }
}