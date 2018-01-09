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
Class Apptha_Productfilters_CatalogSearch_ResultController extends Mage_Core_Controller_Front_Action{
    /**
     * To get Sessionfrom catalog module
     * @return object
     */
    protected function _getSession() {
        return Mage::getSingleton ( 'catalog/session' );
    }
    /**
     * Function index
     * @return void
     *
     */
    public function indexAction() {
        $params = $this->getRequest ()->getParams ();
        $responseData = array ();
        $searchQuery = Mage::helper ( 'catalogsearch' )->getQuery ();
        $searchQuery->setStoreId ( Mage::app ()->getStore ()->getId () );
        if ($searchQuery->getQueryText () != '') {
            if (Mage::helper ( 'catalogsearch' )->isMinQueryLength ()) {
                $searchQuery->setId ( 0 )->setIsActive ( 1 )->setIsProcessed ( 1 );
            } else {
                if ($searchQuery->getId ()) {
                    $searchQuery->setPopularity ( $searchQuery->getPopularity () + 1 );
                } else {
                    $searchQuery->setPopularity ( 1 );
                }

                if ($searchQuery->getRedirect ()) {
                    $searchQuery->save ();
                    $this->getResponse ()->setRedirect ( $searchQuery->getRedirect () );
                    return;
                } else {
                    $searchQuery->prepare ();
                }
            }
            Mage::helper ( 'catalogsearch' )->checkNotes ();
            $this->loadLayout ();
            if ($params ['isAjax'] == 1) {
                $viewPanelData = $this->getLayout ()->getBlock ( 'catalogsearch.leftnav' )->toHtml ();
                $productListData = $this->getLayout ()->getBlock ( 'search_result_list' )->toHtml ();
                $responseData ['status'] = 'SUCCESS';
                $responseData ['viewpanel'] = $viewPanelData;
                $responseData ['productlist'] = $productListData;
                $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $responseData ) );
                return;
            }
            $this->_initLayoutMessages ( 'catalog/session' );
            $this->_initLayoutMessages ( 'checkout/session' );
            $this->renderLayout ();
            if (! Mage::helper ( 'catalogsearch' )->isMinQueryLength ()) {
                $searchQuery->save ();
            }
        } else {
            $this->_redirectReferer ();
        }
    }
}