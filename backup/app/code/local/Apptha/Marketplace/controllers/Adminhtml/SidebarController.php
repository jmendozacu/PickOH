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
 * This class contains banner slider save and edit action
 * which used to admin grid banner slide
 */
class Apptha_Marketplace_Adminhtml_SidebarController extends Mage_Adminhtml_Controller_Action {
    /**
     * Init action for banner slider
     * return layout file
     */
    protected function _initAction() {
        /**
         * Return the admin grid layout
         */
        $sidebarTitle='Sidebar Slider';
        $this->loadLayout ()->_setActiveMenu ( 'marketplace/items' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( $sidebarTitle ), Mage::helper ( 'adminhtml' )->__ ( $sidebarTitle ) );
        return $this;
    }
    /**
     * To set title and layout for banner slider
     * @return void
     */
    public function indexAction() {
        $sidebarHeading='Sidebar';
        $this->_title ( $this->__ ( $sidebarHeading ) );
        $this->_initAction ()->renderLayout ();
    }
    /**
     * New action forward to exit funcation
     * Forword the edit action
     * @return void
     */
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    
    /**
     * Edit action for banner slider
     * Set the edit action id
     * @return void
     */
    public function editAction() {
        $sidebarId = $this->getRequest ()->getParam ( 'id' );
        $sidebarModel = Mage::getModel ( 'marketplace/sidebar' )->load ( $sidebarId );
        if ($sidebarModel->getId () || $sidebarId == 0) {
            $post = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            if (! empty ( $post )) {
                $sidebarModel->setData ( $post );
            }
            Mage::register ( 'marketplace_data', $sidebarModel );
            $this->_title ( $this->__ ( 'Sidebar' ) )->_title ( $this->__ ( 'Manage Sidebar' ) );
            if ($sidebarModel->getId ()) {
                $this->_title ( $sidebarModel->getTitle () );
            } else {
                $sidebarTitle= 'New Sidebar Banner';
                $this->_title ( $this->__ ( $sidebarTitle ) );
            }
            $this->loadLayout ();
            $this->_setActiveMenu ( 'marketplace/items' );
            $adminhtmlHelper=Mage::helper ( 'adminhtml' );
            $this->_addBreadcrumb ( $adminhtmlHelper->__ ( 'Item Manager' ), $adminhtmlHelper->__ ( 'Item Manager' ) );
            $this->_addBreadcrumb ( $adminhtmlHelper->__ ( 'Item News' ), $adminhtmlHelper->__ ( 'Item News' ) );
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            $this->_addContent ( $this->getLayout ()->createBlock ( 'marketplace/adminhtml_sidebar_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'marketplace/adminhtml_sidebar_edit_tabs' ) );
            $this->renderLayout ();
        } else {
            $marketplaceHelper=Mage::helper ( 'marketplace' );
            Mage::getSingleton ( 'adminhtml/session' )->addError ($marketplaceHelper->__ ( 'Item does not exist' ) );
            $this->_redirect ( '*/*/' );
        }
    }
    
    /**
     * Function to save sidebar slider
     * @return void
     */
    public function saveAction() {
        /**
         * Get Data
         */ 
        if ($postValues = $this->getRequest ()->getPost ()) {
            if (isset ( $postValues ['file'] ['delete'] ) && $postValues ['file'] ['delete'] == 1) {
                $postValues ['file'] = '';
            } elseif (isset ( $postValues ['file'] ) && is_array ( $postValues ['file'] )) {
                $postValues ['file'] = $postValues ['file'] ['value'];
            }
            $file = new Varien_Io_File ();
            $imageDirPath = Mage::getBaseDir ( 'media' ) . DS . 'sliderimages';
            $thumbimageyDir = Mage::getBaseDir ( 'media' ) . DS . 'sliderimages' . DS . 'thumbs';
            if (! is_dir ( $imageDirPath )) {
                $file->mkdir ( $imageDirPath, 0777 );
            }
            if (! is_dir ( $thumbimageyDir )) {
                $file->mkdir ( $thumbimageyDir, 0777 );
            }
            if (isset ( $_FILES ['file'] ['name'] ) && $_FILES ['file'] ['name'] != '') {
                try {
                    $uploaderObj = new Varien_File_Uploader ( 'file' );
                    $uploaderObj->setAllowedExtensions ( array (
                            'jpg', 'jpeg',
                            'gif','png' 
                    ) );
                    $uploaderObj->setAllowRenameFiles ( true );
                    $uploaderObj->setFilesDispersion ( true );
                    $filePath = $imageDirPath . DS;
                    $result = $uploaderObj->save ( $filePath, $_FILES ['file'] ['name'] );
                    $file = str_replace ( DS, '/', $result ['file'] );
                    $imageUrl = Mage::getBaseDir('media').DS."sliderimages".$file;
                    $imageResized = Mage::getBaseDir ( 'media' ) . DS . "sliderimages" . DS . "thumbs" . DS . "sliderimages" . $file;
                    if (! file_exists ( $imageResized ) && file_exists ( $imageUrl )) :
                        $imageObject = new Varien_Image ( $imageUrl );
                        $imageObject->constrainOnly ( TRUE );
                        $imageObject->keepAspectRatio ( FALSE );
                        $imageObject->keepFrame ( FALSE );
                        $imageObject->quality ( 100 );
                        $imageObject->resize ( 250, 250 );
                        $imageObject->save ( $imageResized );
                    endif;
                    $postValues ['file'] = 'sliderimages' . $file;
                } catch ( Exception $e ) {
                    $postValues ['file'] = 'sliderimages' . '/' . $_FILES ['file'] ['name'];
                    Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'marketplace' )->__ ( 'Invalid file format upload attempt' ) );
                    $this->_redirect ( '*/*/' );
                    }
            }
            $sidebarBannerModel = Mage::getModel ( 'marketplace/sidebar' );
            $sidebarBannerModel->setData ( $postValues )->setId ( $this->getRequest ()->getParam ( 'id' ) );
            try {
                if ($sidebarBannerModel->getCreatedTime == NULL || $sidebarBannerModel->getUpdateTime () == NULL) {
                    $sidebarBannerModel->setCreatedTime ( date ( "Y-m-d H:i:s", Mage::getModel ( 'core/date' )->timestamp ( time () ) ) )->setUpdateTime ( date ( "Y-m-d H:i:s", Mage::getModel ( 'core/date' )->timestamp ( time () ) ) );
                } else {
                    $sidebarBannerModel->setUpdateTime ( date ( "Y-m-d H:i:s", Mage::getModel ( 'core/date' )->timestamp ( time () ) ) );
                }
                $sidebarBannerModel->save ();
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Slider Image have been saved successfully' ) );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( false );
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $sidebarBannerModel->getId () 
                    ) );
                    return;
                }
                $this->_redirect ( '*/*/' );
                return;
            } catch ( Exception $e ) {
                $adminhtmlSessoion=Mage::getSingleton ( 'adminhtml/session' );
                $adminhtmlSessoion->addError ( $e->getMessage () );
                $adminhtmlSessoion->setFormData ( $postValues );
                $this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' ) ) );
                return;
            }
        }
        Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'marketplace' )->__ ( 'Unable to save Slider Image' ) );
        $this->_redirect ( '*/*/' );
    }
    /**
     * Mass delete action for banners
     * @return void
     */
    public function massDeleteAction() {
        $sidebarSliderIds = $this->getRequest ()->getParam ( 'marketplace' );
        if (! is_array ( $sidebarSliderIds )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $sidebarSliderIds as $bannersliderId ) {
                    $sidebarslider = Mage::getModel ( 'marketplace/sidebar' )->load ( $bannersliderId );
                    $sidebarslider->delete ();
                }
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were deleted successfully', count ( $sidebarSliderIds ) ) );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Delete action for banner
     * @return void
     */
    public function deleteAction() {
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                $sidebarModelObject = Mage::getModel ( 'marketplace/sidebar' );
                $sidebarModelObject->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Slider Image have been deleted successfully !' ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError($e->getMessage());
                $this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam('id')));
            }
        }
        $this->_redirect ( '*/*/' );
    }
    /**
     * Mass status update action for banner
     * @return void
     */
    public function massStatusAction() {
       $sidebarbannerSliderIds = $this->getRequest ()->getParam ( 'marketplace' );
        if (! is_array ( $sidebarbannerSliderIds )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $this->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $sidebarbannerSliderIds as $sidebarbannerSliderId ) {
                    Mage::getSingleton ( 'marketplace/sidebar' )->load ( $sidebarbannerSliderId )->setStatus ( $this->getRequest ()->getParam ( 'status' ) )->save ();
                }
                $this->_getSession ()->addSuccess ( $this->__ ( 'Total of %d record(s) were updated successfully', count ( $sidebarbannerSliderIds ) ) );
            } catch ( Exception $e ) {
                $this->_getSession ()->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
}