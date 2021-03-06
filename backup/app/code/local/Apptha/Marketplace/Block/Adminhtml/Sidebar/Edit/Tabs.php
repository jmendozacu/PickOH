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
 * This class contains form tab action
 */
class Apptha_Marketplace_Block_Adminhtml_Sidebar_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{

/**
 * Set form id and title
 * @return void
 */
  public function __construct(){
      parent::__construct();
      $this->setDestElementId('edit_form');
      $this->setId('marketplace_tabs');
      $this->setTitle(Mage::helper('marketplace')->__('Sidebar Information'));
  }
  /**
   * Set layout block
   * @return object
   */
  protected function _beforeToHtml(){
     $this->addTab('form_section', array(
          'label'     => Mage::helper('marketplace')->__('Sidebar Information'),
          'title'     => Mage::helper('marketplace')->__('Sidebar Information'),
          'content'   => $this->getLayout()->createBlock('marketplace/adminhtml_sidebar_edit_tab_form')->toHtml()));  
     return parent::_beforeToHtml();
  }
}