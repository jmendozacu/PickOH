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
 * Function written in this file are used for system configuration validation
 */
class Apptha_Marketplace_Model_Key extends Mage_Core_Model_Config_Data {
    /**
     * Validate license key
     */
    public function save() {
        $apikey = $this->getValue ();
        $marketplaceApiKey = Mage::helper ( 'marketplace' )->marketplaceApiKey ();
        if ($apikey != $marketplaceApiKey) {
            Mage::getSingleton ( 'core/session' )->addNotice ( base64_decode ( 'PGEgdGFyZ2V0PSJfYmxhbmsiIGhyZWY9Imh0dHA6Ly93d3cuYXBwdGhhLmNvbS9jaGVja291dC9jYXJ0L2FkZC9wcm9kdWN0LzE1NiIgc3R5bGU9ImNvbG9yOnJlZDsiPkludmFsaWQgTGljZW5zZSBLZXkgLSBCdXkgbm93PC9hPg==' ) );
            return true;
        }
        return parent::save ();
    }
}