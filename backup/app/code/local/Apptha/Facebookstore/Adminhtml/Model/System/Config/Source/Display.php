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
class Apptha_Facebookstore_Adminhtml_Model_System_Config_Source_Display{

    /**
     * Functions to get Options
     * @return array
     *
     */
    public function toOptionArray() {
        return array (
                array (
                        'value' => standard,
                        'label' => Mage::helper ( 'adminhtml' )->__ ( 'Standard' )
                ),
                array (
                        'value' => bottom,
                        'label' => Mage::helper ( 'adminhtml' )->__ ( 'Window right bottom corner' )
                )
        )
        ;
    }
}