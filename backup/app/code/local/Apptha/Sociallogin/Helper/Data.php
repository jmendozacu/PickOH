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
 * This file is used to get twitter url and license key functionality
 *
 * In this class, get twitter url and license key operations are included.
 */
class Apptha_Sociallogin_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Get Twitter authendication URL
     *
     * @return string Twitter authendication URL
     */
    public function getTwitterUrl($data) {
        require 'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';

        $twitteroauth = new TwitterOAuth ( YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET );

        /**
         * Request to authendicate token, the @param string URL redirects the authorize page
         */
        if ($data == 1) {
            $requestToken = $twitteroauth->getRequestToken ( Mage::getBaseUrl () . 'sociallogin/index/twitterlogin?fb=1' );
        } else {
            $requestToken = $twitteroauth->getRequestToken ( Mage::getBaseUrl () . 'sociallogin/index/twitterlogin' );
        }
        /**
         * check condition http code is equal to 200
         */
        if ($twitteroauth->http_code == 200) {
            Mage::getSingleton ( 'customer/session' )->setTwToken ( $requestToken ['oauth_token'] );
            Mage::getSingleton ( 'customer/session' )->setTwSecret ( $requestToken ['oauth_token_secret'] );
            return $twitteroauth->getAuthorizeURL ( $requestToken ['oauth_token'] );
        }
    }
    /**
     * Retrieve customer session from core customer session
     *
     * @return array
     */
    public function _getCustomerSession() {
        return Mage::getSingleton ( 'customer/session' );
    }
    /**
     * Check license key
     *
     * @return bool either True | False
     */
    public function checkSocialloginkey() {
        /**
         * get Sociallogin key
         */
        $genKey = $this->socialloginKey ();
        $license = Mage::getStoreConfig ( 'sociallogin/general/license' );
        /**
         * declare return value
         */
        $returnValue = '';
        /**
         * check condition license key equal to genkey
         */
        if ($genKey == $license) {
            $returnValue = true;
        } else {
            $returnValue = false;
        }
        return $returnValue;
    }
    /**
     * Generates the Social login Key
     *
     * @return string social login license key message
     */
    public function socialloginKey() {
        $code = $this->genenrateOscdomain ();
        return substr ( $code, 0, 25 ) . "CONTUS";
    }
    /**
     * Generates the Domain Key
     *
     * @return string $enc_message
     */
    public function domainKey($tkey) {
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $lentkey = strlen ( $tkey );
        for($i = 0; $i < $lentkey; $i ++) {
            $key_array [] = $tkey [$i];
        }
        /**
         * assign encrypted message variable is empty string
         */
        $enc_message = "";
        $kPos = 0;
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $lenstrchnars = strlen ( $chars_str );
        for($i = 0; $i < $lenstrchnars; $i ++) {
            $chars_array [] = $chars_str [$i];
        }
        $lenmessage = strlen ( $message );
        $countKeyArray = count ( $key_array );
        for($i = 0; $i < $lenmessage; $i ++) {
            $char = substr ( $message, $i, 1 );

            $offset = $this->getOffset ( $key_array [$kPos], $char );
            /**
             * assign encrypted message to customer
             */
            $enc_message .= $chars_array [$offset];
            $kPos ++;
            /**
             * check condition country key array equal to kpos
             */
            if ($kPos >= $countKeyArray) {
                $kPos = 0;
            }
        }
        /**
         * @return encrypted message to customer
         */
        return $enc_message;
    }
    /**
     * Get offset from character string
     *
     * @return integer $offset
     */
    public function getOffset($start, $end) {
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        /**
         * get string length
         */
        $countcharstr = strlen ( $charsStr );
        /**
         * load string length for get country array value
         */
        for($i = 0; $i < $countcharstr; $i ++) {
            $charsArray [] = $charsStr [$i];
        }
        $countcharsArray = count ( $charsArray );
        /**
         * load country array items
         */
        for($i = $countcharsArray - 1; $i >= 0; $i --) {
            $lookupObj [ord ( $charsArray [$i] )] = $i;
        }

        $sNum = $lookupObj [ord ( $start )];
        $eNum = $lookupObj [ord ( $end )];

        $offset = $eNum - $sNum;
        /**
         * check condition offset value is equal lessthan 0
         */
        if ($offset < 0) {
            $counrArray = count ( $charsArray );
            $offsetData = $counrArray + ($offset);
        }

        return $offsetData;
    }
    /**
     * Generates the Domain
     *
     * @return string $response
     */
    public function genenrateOscdomain() {
        /**
         * Get Controller Name
         */
        $strDomainName = Mage::app ()->getFrontController ()->getRequest ()->getHttpHost ();
        preg_match ( "/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder [2], $matches );
        /**
         * check condition strdomain is not empty
         */
        if (isset ( $matches ['domain'] )) {
            $customerUrl = $matches ['domain'];
        } else {
            $customerUrl = "";
        }
        /**
         * Replace Customer URL
         */
        $customerUrl = str_replace ( "www.", "", $customerUrl );
        $customerUrl = str_replace ( ".", "D", $customerUrl );
        /**
         * Convert to upper case
         */
        $customerUrl = strtoupper ( $customerUrl );
        /**
         * check condition strdomain value is not empty
         */
        if (isset ( $matches ['domain'] )) {
            /**
             * Domain key customer url
             */
            $response = $this->domainKey ( $customerUrl );
        } else {
            $response = "";
        }
        return $response;
    }

    /**
     * Get auth url
     *
     * @param array $session
     * @param string $sellerLogin
     * @return string $session
     */
    public function getAuthUrl($session, $sellerLogin) {
        if ($sellerLogin == 1) {
            /**
             * set Auth url
             */
            $session->setBeforeAuthUrl ( Mage::helper ( 'marketplace/marketplace' )->dashboardUrl () );
        } else {
            /**
             * set Auth url
             */
            $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getAccountUrl () );
        }
        return $session;
    }
    /**
     * Check auth url
     *
     * @param array $session
     * @return number $checkAuthUrl
     */
    public function checkAuthUrl($session) {
        $checkAuthUrl = 0;
        if (! $session->getBeforeAuthUrl () || $session->getBeforeAuthUrl () == Mage::getBaseUrl ()) {
            $checkAuthUrl = 1;
        }
        return $checkAuthUrl;
    }

    /**
     * Function to get customer data
     * @return object
     */
    public function getSellerData($adminApproval,$data,$customer,$groupId){
        if ($adminApproval == 1 && $data == 1) {
            $customer->setGroupId ( $groupId );
            $customer->setCustomerstatus ( '0' );
        } elseif ($adminApproval != 1 && $data == 1) {
            $customer->setGroupId ( $groupId );
            $customer->setCustomerstatus ( '1' );
        } else {
            $customer->setCustomerstatus ( '1' );
        }
        return  $customer;
    }
    /**
     * Function to get validate customer
     * @return array
     *
     */
    public function getValidateData($validationCustomer,$errors){
        if (is_array ( $validationCustomer )) {
            return  array_merge ( $validationCustomer, $errors );
        }
    }
    /**
     * Function to get Exploed link
     * @return string
     */
    public function getExplodedLink($link){
        if (! empty ( $link )) {
            $splitLink = explode ( Mage::getBaseUrl (), $link );
            return  end ( $splitLink );
        }
    }
    /**
     * Function to check current customer
     * @param unknown $customer
     * @return Mage_Core_Model_Abstract|mixed|NULL
     */
    public function checkCurrentCustomer($customer){
        if (! $customer = Mage::registry ( 'current_customer' )) {
            $customer = Mage::getModel ( 'customer/customer' )->setId ( null );
        }
        return $customer;
    }
    public function checkConfirmation($magentoVersion,$confirmation,$customer){
        if (version_compare ( $magentoVersion, '1.9.1', '>=' )) {
            $customer->setPasswordConfirmation ($confirmation);
        } else {
            $customer->setConfirmation ($confirmation);
        }
    }
    /**
     * Function to check address errors
     * @return array
     *
     */
    public function checkAddressErrors($errors,$addressErrors){
        if (is_array ( $addressErrors )) {
            return  $errors = array_merge ( $errors, $addressErrors );
        }
    }
    /**
     *
     * Function to set seller signup data
     * @param unknown $customer
     * @param unknown $adminApproval
     * @param unknown $sellerRegisteration
     * @param unknown $customerId
     * @param unknown $session
     * @return boolean
     */
    public function setSellerSignup($customer,$adminApproval,$sellerRegisteration,$customerId,$session){


        $customerHelper=Mage::helper('sociallogin/system')->getCustomerHelper();
        if ($customer->isConfirmationRequired () && $adminApproval == 1 && $sellerRegisteration == 1) {
            $customer->sendNewAccountEmail ( 'confirmation', $session->getBeforeAuthUrl (), Mage::app ()->getStore ()->getId () );
            Mage::getModel ( 'marketplace/sellerprofile' )->adminApproval ( $customerId );
            $session->addSuccess ( $this->__ ( 'Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.',$customerHelper->getEmailConfirmationUrl ( $customer->getEmail () ) ) );
            return true;
        }
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation', $session->getBeforeAuthUrl (), Mage::app ()->getStore ()->getId () );
            $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', $customerHelper->getEmailConfirmationUrl ( $customer->getEmail () ) ) );
            return true;

        }
    }
    /**
     *
     * Function to send sign up mail
     * @param unknown $sellerRegisteration
     * @param unknown $adminApproval
     * @param unknown $customerId
     */
    public function sendSignUpMail($sellerRegisteration,$adminApproval,$customerId){
        if($sellerRegisteration==1 && $adminApproval==0){
            $getTemplateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/seller_email_template_selection' );
            $adminEmailIdVal = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
            $getToMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/email" );
            $getToName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/name" );
            ($getTemplateId)?$getEmailTemplateForSeller = Mage::getModel ( 'core/email_template' )->load ( $getTemplateId ):$getEmailTemplateForSeller = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_seller_email_template_selection' );
            $customerData = Mage::getModel ( 'customer/customer' )->load ( $customerId );
            $getRecipient = $customerData->getEmail ();
            $getCustomerName = $customerData->getName ();
            $getEmailTemplateForSeller->setSenderEmail ( $getToMailId );
            $getEmailTemplateForSeller->setSenderName ( ucwords ( $getToName ) );
            $emailTemplateForSellerVariables = (array ('cname' => ucwords ( $getCustomerName ),'ownername' => ucwords ( $getToName )));
            $getEmailTemplateForSeller->setDesignConfig ( array ('area' => 'frontend'));
            $getEmailTemplateForSeller->getProcessedTemplate ( $emailTemplateForSellerVariables );
            $getEmailTemplateForSeller->send ( $getRecipient, ucwords ( $getCustomerName ), $emailTemplateForSellerVariables );
        }
    }
    /**
     * Function to get Link
     * @return string
     */
    public function getLink($link){
        if (! empty ( $link )) {
            return trim ( $link, '/' );
        }
    }
    /**
     * Function to check subscribed
     * @return void
     */
    public function checksubscribed($customer,$isSubscribed){
        if ($isSubscribed) {
            $customer->setIsSubscribed ( 1 );
        }
    }
}