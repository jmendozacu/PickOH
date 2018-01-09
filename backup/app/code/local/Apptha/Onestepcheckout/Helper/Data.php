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
class Apptha_Onestepcheckout_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * get the Onestepcheckout activated settings and return the boolean
     */
    public function isOnepageCheckoutLinksEnabled() {
        /**
         * return store config which is onestep checkout extension enable or not
         */
        return Mage::getStoreConfig ( 'onestepcheckout/general/Activate_apptha_onestepcheckout' );
    }
    /**
     * Generate the Onestep API Key
     * REturn @string
     */
    public function onestepApiKey() {
        /**
         * generate domain key
         */
        $code = $this->genenrateOscdomain ();
        /**
         * Return the domain key string ends with 'contus'
         */
        return substr ( $code, 0, 25 ) . "CONTUS";
    }
    /**
     * DomainKey function for return the encrypted message
     * return @string
     */
    public function domainKey($tkey) {
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $lenkey = strlen ( $tkey );
        for($i = 0; $i < $lenkey; $i ++) {
            $key_array [] = $tkey [$i];
        }
        /**
         * Assign the encrypted message to empty
         */
        $enc_message = "";
        /**
         * Position value set as zero
         */
        $kPos = 0;
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $lenCharStr = strlen ( $chars_str );
        for($i = 0; $i < $lenCharStr; $i ++) {
            $chars_array [] = $chars_str [$i];
        }
        $messagelen = strlen ( $message );
        $countKeyArray = count ( $key_array );
        for($i = 0; $i < $messagelen; $i ++) {
            $char = substr ( $message, $i, 1 );

            $offset = $this->getOffset ( $key_array [$kPos], $char );
            $enc_message .= $chars_array [$offset];
            /**
             * Increment the position value
             */
            $kPos ++;
            if ($kPos >= $countKeyArray) {
                $kPos = 0;
            }
        }
        /**
         * Rerturn encrypted message
         */
        return $enc_message;
    }
    /**
     * Licence() - function return teh text with "licence"
     * Return $string
     */
    public function license() {
        return 'license';
    }
    /**
     * getOffset - Function which for is string offset
     * Return @string
     */
    public function getOffset($start, $end) {
        /**
         * character string
         */
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $charlenstr = strlen ( $chars_str );
        for($i = 0; $i < $charlenstr; $i ++) {
            $chars_array [] = $chars_str [$i];
        }
        $countCharsArr = count ( $chars_array );
        for($i = $countCharsArr - 1; $i >= 0; $i --) {
            $lookupObj [ord ( $chars_array [$i] )] = $i;
        }
        /**
         * getting start
         */
        $sNum = $lookupObj [ord ( $start )];
        /**
         * getting end
         */
        $eNum = $lookupObj [ord ( $end )];
        /**
         * offset
         */
        $offset = $eNum - $sNum;

        if ($offset < 0) {
            $offset = count ( $chars_array ) + ($offset);
        }
        /**
         * Return $offset as string
         */
        return $offset;
    }
    /**
     * Function to generate Domain
     * return response
     */
    public function genenrateOscdomain() {
        /**
         * getting domain name
         */
        $strDomainName = Mage::app ()->getFrontController ()->getRequest ()->getHttpHost ();
        preg_match ( "/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder [2], $matches );
        if (isset ( $matches ['domain'] )) {
            /**
             * cusomer domain Url
             */
            $customerDomainUrl = $matches ['domain'];
        } else {
            $customerDomainUrl = "";
        }
        /**
         * replacing www with empty character
         */
        $customerDomainUrl = str_replace ( "www.", "", $customerDomainUrl );
        /**
         * replacing with dot
         */
        $customerDomainUrl = str_replace ( ".", "D", $customerDomainUrl );
        $customerDomainUrl = strtoupper ( $customerDomainUrl );
        if (isset ( $matches ['domain'] )) {
            /**
             * domain key
             */
            $response = $this->domainKey ( $customerDomainUrl );
        } else {
            $response = "";
        }
        /**
         * return response
         */
        return $response;
    }
    /**
     * Function to save shipping details
     * @param array $Billingdata,string $Method,array $Paymentresult,array $Paymentresult
     * @return array
     */
    public function getOrderedDetails($Billingdata, $Method, $Paymentresult, $Billingresult) {
	$result=array();
        if (isset ( $Billingdata ['email'] )) {
            $Billingdata ['email'] = trim ( $Billingdata ['email'] );
        }

        if (! empty ( $Billingresult )) {
            $result ['error'] = true;
            $result ['error_messages'] = $Billingresult ['message'];
        }

        if (! empty ( $Paymentresult )) {
            $result ['error'] = true;
            $result ['error_messages'] = $Paymentresult ['message'];
        }
        if (isset ( $Billingdata ['email'] )) {
            if (! Zend_Validate::is ( $Billingdata ['email'], 'EmailAddress' )) {
                $result ['error'] = true;
                $result ['error_messages'] = $this->__ ( 'Invalid Email address' );
            }
            if ($Method == 'register') {
                $cust_exist = Mage::helper ( 'onestepcheckout/checkout' )->IscustomerEmailExists ( $Billingdata ['email'] );
                if ($cust_exist) {
                    $result ['error'] = true;
                    $result ['error_messages'] = $this->__ ( 'Email address Already Exists' );
                }
            }
        }

        return $result;
    }
    /**
     * Function to get  required  agreements
     * @param array $postedAgreements,array $result
     * @return array
     */
    public function getRequiredAgreements($postedAgreements, $result) {
        if ($requiredAgreements = Mage::helper ( 'checkout' )->getRequiredAgreementIds () && (array_diff ( $requiredAgreements, $postedAgreements ))) {
            return $result;
        }

        return $result;
    }
    /**
     * Function to get  required  agreements data
     * @param array $postedAgreementsData,array $result
     * @return array
     */
    public function getRequiredAgreementsData($postedAgreementsData, $result) {
        if ($requiredAgreementsData = Mage::helper ( 'checkout' )->getRequiredAgreementIds () && (array_diff ( $requiredAgreementsData, $postedAgreementsData ))) {

            $result ['error'] = true;
            $result ['success'] = false;
            $result ['error_messages'] = $this->__ ( 'Please agree to all the terms and conditions before placing the order.' );

            return $result;
        }
    }
    /**
     * Function to save shipping details
     * @param array $Billingdata,int $customerAddressId,array $Shippingdata,int $ShippingAddressId
     * @return void
     */
    public function saveShippingDetails($Billingdata, $customerAddressId, $Shippingdata, $ShippingAddressId) {
        if (! empty ( $ShippingAddressId )) {
            $shippingAddress = Mage::getModel ( 'customer/address' )->load ( $ShippingAddressId );
            if (is_object ( $shippingAddress ) && $shippingAddress->getCustomerId () == Mage::helper ( 'customer' )->getCustomer ()->getId ()) {
                $Shippingdata = array_merge ( $Shippingdata, $shippingAddress->getData () );
                $shipping_result = Mage::getSingleton ( 'checkout/type_onepage' )->saveShipping ( $Shippingdata, $ShippingAddressId );
            }
        } else if (empty ( $Billingdata ['use_for_shipping'] ) && ! $ShippingAddressId) {
            $shipping_result = Mage::getSingleton ( 'checkout/type_onepage' )->saveShipping ( $Shippingdata, $ShippingAddressId );
        } else {
            $shipping_result = Mage::getSingleton ( 'checkout/type_onepage' )->saveShipping ( $Billingdata, $customerAddressId );
        }
    }
    
    
    /**
     * Function to get seller collection
     * @return object
     * 
     */
    public function getSellerProfileCollection($sellerId){
        return Mage::getModel('marketplace/sellerprofile')->getCollection()->addFieldToFilter('seller_id', $sellerId);
    }
}
