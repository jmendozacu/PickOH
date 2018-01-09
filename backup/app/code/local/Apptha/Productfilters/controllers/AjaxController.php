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
class Apptha_Productfilters_AjaxController extends  Mage_Core_Controller_Front_Action{
    /**
     * Function Index action
     *
     * @return void
     *
     */
    public function indexAction() {
        $catalogSearchHelper = Mage::helper ( 'catalogsearch' );
        $params = $this->getRequest ()->getParams ();
        $response = array ();
        $query = $catalogSearchHelper->getQuery ();
        $query->setStoreId ( Mage::app ()->getStore ()->getId () );
        if ($query->getQueryText () != '') {
            if ($catalogSearchHelper->isMinQueryLength ()) {
                $query->setId ( 0 )->setIsActive ( 1 )->setIsProcessed ( 1 );
            } else {
                if ($query->getId ()) {
                    $query->setPopularity ( $query->getPopularity () + 1 );
                } else {
                    $query->setPopularity ( 1 );
                }

                if ($query->getRedirect ()) {
                    $query->save ();
                    $this->getResponse ()->setRedirect ( $query->getRedirect () );
                    return;
                } else {
                    $query->prepare ();
                }
            }
            $catalogSearchHelper->checkNotes ();

            $this->loadLayout ();
            if ($params ['isAjax'] == 1) {
                $viewpanel = $this->getLayout ()->getBlock ( 'catalogsearch.leftnav' )->toHtml ();
                $productlist = $this->getLayout ()->getBlock ( 'search_result_list' )->toHtml ();
                $response ['status'] = 'SUCCESS';
                $response ['viewpanel'] = $viewpanel;
                $response ['productlist'] = $productlist;
                $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $response ) );
                return;
            }
            $this->_initLayoutMessages ( 'catalog/session' );
            $this->_initLayoutMessages ( 'checkout/session' );
            $this->renderLayout ();
            if (! $catalogSearchHelper->isMinQueryLength ()) {
                $query->save ();
            }
        } else {
            $this->_redirectReferer ();
        }
    }
}