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
?>
<?php

/**
 * This class contains grid details
 */
class Apptha_Marketplace_Block_Adminhtml_Sidebar_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * Set grid id and sort order
     */
public function __construct() {
        parent::__construct ();
        $getsidebarObject = $this;
        $getsidebarObject->setDefaultSort($this->bannerId);
        $getsidebarObject->setDefaultDir('ASC');
        $getsidebarObject->setId('sidebarGrid');
        $getsidebarObject->setSaveParametersInSession ( true );
    }
    /**
     * Get banner sliders collection
     */
      protected function _prepareCollection() {
        $sidebarSlideCollection = Mage::getModel ( 'marketplace/sidebar' )->getCollection ();
        $this->setCollection ( $sidebarSlideCollection );
        return parent::_prepareCollection ();
    }
    /**
     * Prepare banner slider columns
     */
    protected function _prepareColumns() {
       $bannerId=  array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'ID' ),
                'align' => 'right',
                'width' => '50px',
                'index' => 'banner_id'
         );
        $this->addColumn ('banner_id',$bannerId);
        /**
         * Add banner title
         */
        $bannerTitle=array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Banner Title' ),
                'align' => 'left',
                'index' => 'title'
        );

        $this->addColumn ( 'title',$bannerTitle);
        /**
         * Add banner image.
         */
        $bannerImage=array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Banner Image' ),
                'align' => 'center',
                'index' => 'image',
                'filter' => false,
                'sortable' => false,
                'width' => '150',
                'renderer' => 'marketplace/adminhtml_renderer_image'
        );

        $this->addColumn ( 'image', $bannerImage);
        /**
         * Add sort order.
         */
        $sortOrder=array ('header' => Mage::helper ( 'marketplace' )->__ ( 'Sort Order' ),
                'align' => 'left',
                'width' => '80px',
                'index' => 'sort'
        );
        $this->addColumn ( 'sort', $sortOrder );
        /**
         * Add status.
         */
        $status=array('header' => Mage::helper ( 'marketplace' )->__ ( 'Status' ),
                'align' => 'left','width' => '80px',
                'index' => 'status','type' => 'options','options' => array (1 => 'Enabled',
                        2 => 'Disabled') );
        $this->addColumn ( 'status',$status);
        /**
         * Add banner action.
         */
        $actionColumn=array ('header' => Mage::helper ( 'marketplace' )->__('Action'),
                'width' => '100', 'type' => 'action','getter' => 'getId',
                'actions' => array (array ( 'caption' => Mage::helper ( 'marketplace' )->__ ( 'Edit' ),
                                'url' => array ( 'base' => '*/*/edit'  ),
                                'field' => 'id')),
                'filter' => false,'sortable' => false,'index' => 'stores','is_system' => true);

        $this->addColumn ( 'action', $actionColumn );
        return parent::_prepareColumns ();
    }
    /**
     * Prepare banner slide mass action
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField ( 'banner_id' );
        $this->getMassactionBlock ()->setFormFieldName ( 'marketplace' );
        /**
         * Add delete option.
         */
        $this->getMassactionBlock ()->addItem ( 'delete', array ('label' => Mage::helper ( 'marketplace' )->__ ( 'Delete' ),                'url' => $this->getUrl ( '*/*/massDelete' ),
                'confirm' => Mage::helper ( 'marketplace' )->__ ( 'Are you sure?' )) );
        $sidebarStatus = Mage::getSingleton ( 'marketplace/status' )->getStatusArray();
        array_unshift ( $sidebarStatus, array ('label' => '','value' => '') );
        /**
         * Add change status option.
         */
        $this->getMassactionBlock ()->addItem ( 'status', array ('label' => Mage::helper ( 'marketplace' )->__ ( 'Change status' ),
                'url' => $this->getUrl ( '*/*/massStatus', array ( '_current' => true ) ),
                'additional' => array ('visibility' => array (
                                'name' => 'status','type' => 'select',
                                'class' => 'required-entry','label' => Mage::helper ( 'marketplace' )->__ ( 'Status' ),
                                'values' => $sidebarStatus ) )) );
        return $this;
    }

    /**
     * Getting row url
     */
    public function getRowUrl($row) {
       return $this->getUrl ( '*/*/edit', array (
                'id' => $row->getId ()
        ) );
    }
}