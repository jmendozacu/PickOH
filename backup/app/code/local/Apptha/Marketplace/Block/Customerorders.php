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
 * Customer Orders 
 */
class Apptha_Marketplace_Block_Customerorders extends Mage_Sales_Block_Order_History {
    /* Rewrites the My Orders Page */
    public function __construct() {
        parent::__construct ();
        $filterState = $this->getRequest ()->getParam ( 'filter_order_state' );
        $orders = Mage::getResourceModel ( 'sales/order_collection' )->addFieldToSelect ( '*' )->addFieldToFilter ( 'customer_id', Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId () );
        switch ($filterState) {
            case 1 :
                $orders->addFieldToFilter ( 'state', array (
                    'in' => Mage::getSingleton ( 'sales/order_config' )->getVisibleOnFrontStates () 
                ) );
                break;
            
            case 2 :
                $orders->addFieldToFilter ( 'state', array (
                    'in' => 'new' 
                ) );
                break;
            case 3 :
                $orders->addFieldToFilter ( 'state', array (
                    'in' => 'processing' 
                ) );
                break;
            
            case 4 :
                $orders->addFieldToFilter ( 'state', array (
                    'in' => 'complete' 
                ) );
                break;
            case 5 :
                $orders->addFieldToFilter ( 'state', array (
                    'in' => 'canceled' 
                ) );
                break;
            
            default :
                $orders->addFieldToFilter ( 'state', array (
                    'in' => Mage::getSingleton ( 'sales/order_config' )->getVisibleOnFrontStates () 
                ) );
                break;
        }
        
        $orders->setOrder ( 'created_at', 'desc' );        
        
        $this->setOrders ( $orders );
        
        Mage::app ()->getFrontController ()->getAction ()->getLayout ()->getBlock ( 'root' )->setHeaderTitle ( Mage::helper ( 'sales' )->__ ( 'My Orders' ) );
    }
}
