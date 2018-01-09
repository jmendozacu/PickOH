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
class Apptha_Marketplace_Block_Result extends Mage_CatalogSearch_Block_Result{
   /**
     * Set search available list orders
     *
     * @return Apptha_Marketplace_Block_Result
     */
    public function setListOrders(){
    /**
     * Get Current Category
     * @var int
     */
        $category = Mage::getSingleton('catalog/layer')->getCurrentCategory();
        /**
         * Get Available Sort Orders
         */
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        $availableOrders = array_merge(array(
            'total_sales' => $this->__('Total sales of the product')
        ), $availableOrders);
/**
 * Sort by desc order and total sales
 */
        $this->getListBlock()
            ->setAvailableOrders($availableOrders)
            ->setDefaultDirection('desc')
            ->setSortBy('total_sales');
        return $this;
    }
}