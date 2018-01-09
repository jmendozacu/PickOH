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
class Apptha_Marketplace_Block_Subscription_Subscribe extends Mage_Core_Block_Template {
    /**
     * Define repeted string variables
     */
    public $strMarketplaceSubscriptionplans = 'marketplace/subscriptionplans';
    
    /**
     * Load layout function
     */
    protected function _prepareLayout() {
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper ( 'marketplace' )->__ ( 'Subscription' ) );
        return parent::_prepareLayout ();
    }
    /**
     * Function to get subscription details page
     *
     * Return the subscription url
     *
     * @return string
     */
    public function subscribeUrl() {
        return Mage::getUrl ( 'marketplace/subscription/subscribe' );
    }
    /**
     * Function to get subscription plans
     *
     * Return the subscription plans as array
     *
     * @return array
     */
    public function subscriptionPlans() {
        return Mage::getModel ( 'marketplace/subscriptionplans' )->getCollection ()->addFieldToFilter ( 'flag', 1 )->setOrder ( 'yearly_fee', 'ASC' );
    }
    /**
     * Function to get particular subscription plan info
     *
     * Passed the plan id to retrieve the info
     *
     * @param int $planId
     *            Return plan info as an array
     * @return array
     */
    public function retriveplaninfo($planId) {
        return Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId );
    }
    /**
     * Checking whether customer already subscribed or not
     *
     * Passed the customer id to get the subscription details
     *
     * @param $customerId Return
     *            subscription info as an array
     * @return array
     */
    public function checkSubscribed($customerId) {
        return Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId );
    }
    /**
     *
     * Function to get subscription end date
     * 
     * @return date
     *
     */
    public function getSubscriptionEndDates($subscriptionPeriod, $offerPeriod, $validityPeriod, $offerValidityPeriod, $subscriptionStartDate) {
        if ($subscriptionPeriod == 1 && $offerPeriod == 1) {
            $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod + $offerValidityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
        } elseif ($subscriptionPeriod == 2 && $offerPeriod == 2) {
            $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod + $offerValidityPeriod . ' years', strtotime ( $subscriptionStartDate ) ) );
        } elseif ($subscriptionPeriod == 1 && $offerPeriod == 2) {
            $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $offerValidityPeriod . ' years ' . $validityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
        } elseif ($subscriptionPeriod == 2 && $offerPeriod == 1) {
            $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod . ' years ' . $offerValidityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
        } elseif ($subscriptionPeriod == 2 && $offerPeriod == 0) {
            $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod . ' years ', strtotime ( $subscriptionStartDate ) ) );
        } elseif ($subscriptionPeriod == 1 && $offerPeriod == 0) {
            $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
        }
        
        return $subscriptionEndDate;
    }
    /**
     * Function to get offer period
     *
     * @param unknown $_subscriptionPlans            
     * @param unknown $monthlyFreeSubscription            
     * @param unknown $yearFreeSubsubscription            
     */
    public function getSubscriptionOfferPeriod($_subscriptionPlans, $monthlyFreeSubscription, $yearFreeSubsubscription) {
        if ($_subscriptionPlans->getOfferPeriod () != 0 && $_subscriptionPlans->getOfferValidityPeriod () != 0) {
            echo ' + ';
            echo $_subscriptionPlans->getOfferPeriod () == 1 ? $monthlyFreeSubscription : $yearFreeSubsubscription;
        }
    }
    /**
     * 
     * Function to get plan info
     * @return object
     */
    public function loadSubscriptionPlan($planIdtoUpgrade){
        return Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planIdtoUpgrade, 'plan_id' );
    }
    
} 