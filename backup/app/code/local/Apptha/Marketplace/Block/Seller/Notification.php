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
 * Seller information
 * This class used to display the seller information
 * Display the seller products
 * Display review form
 * Display the Ratings
 * Display the reviews those have been already submitted by users
 */
class Apptha_Marketplace_Block_Seller_Notification extends Mage_Core_Block_Template {
    /**
     * Function to get last order information
     *
     * @return array
     */
   public function getOrder(){
       $session = Mage::getSingleton('checkout/session');
       $order = Mage::getModel('sales/order');
       $order->loadByIncrementId($session->getLastRealOrderId());
       return $order;
   }
   /**
    * Function to get Shipping Id from order
    *
    * @return array
    */
   public function getShippingIdFromOrder($isVirtual,$order){
       return Mage::helper('marketplace/order')->getShippingIdFromOrder($isVirtual,$order);
   }
   /**
    * Function to get seller profile information
    *
    * @return array
    */
   public function getSellerProfile($id){
       return Mage::getModel ( 'marketplace/sellerprofile' )->load ( $id, 'seller_id' );
   }
   /**
    * Function to get product information
    *
    * @return array
    */
   public function getProductInfo($productId){
       return Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );

   }
   /**
    * Function to get seller delivery schedule details
    *
    * @return array
    */
   public function getSellerDeliveryScheduleDetails($deliveryScheduleEnable,$data){
       if ($deliveryScheduleEnable == 1) {
           $deliveryCost = $data ['delivery_cost'];
           $deliveryComment = $data ['delivery_comment'];
           $deliveryDateInfo = $data ['delivery_date'];
           $deliveryTypeInfo = $data ['delivery_type'];
           $deliveryTimeInfo = $data ['delivery_time'];

           return array('deliveryCost'=>$deliveryCost,'deliveryComment'=>$deliveryComment,'deliveryDateInfo'=>$deliveryDateInfo,'deliveryTypeInfo'=>$deliveryTypeInfo,'deliveryTimeInfo'=>$deliveryTimeInfo);
       }

   }
   /**
    * Function to get order information
    *
    * @return array
    */
   public function orderEmailData(){

       $sellerDefaultCountry = '';
       /**
        * Create an array for hold on seller ids
        */
       $sellerIdsArray = array ();
       $nationalShippingPrice = $internationalShippingPrice = $totalSellerAmount = 0;
       /**
        * Get order collection.
        */
       $order = $this->getOrder();
       /**
        * Get order Shippment description.
        */
       $shippingDescription = $order->getShippingDescription();



       $shippingCountryIdentifier = '';
       $itemCount = 0;
       $items = $order->getAllItems ();
       $orderEmailData = array ();
       /**
        * Get all item from the order collection,Get product id,Get created At
        */
       foreach ( $items as $item ) {
           $shippingSellerShippingPrice = $sellerShippingPrice = 0;

           $getProductId = $item->getProductId ();
           $paymentCode = $order->getPayment ()->getMethodInstance ()->getCode ();
           $productInformation = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $getProductId );

           $productType = $productInformation->getTypeID ();
           $sellerId = $productInformation->getSellerId ();
           /**
            * Get the shipping active status of seller
            */
           $sellerShippingEnabled = Mage::getStoreConfig ( 'carriers/apptha/active' );
           if ($sellerShippingEnabled == 1 && $productType == 'simple') {
               /**
                * Get the product national shipping price,and international shipping price,and shipping country
                */
               $internationalShippingPrice = $productInformation->getInternationalShippingPrice ();
               $nationalShippingPrice = $productInformation->getNationalShippingPrice ();

               $sellerDefaultCountry = $productInformation->getDefaultCountry ();
               $shippingCountryIdentifier = $order->getShippingAddress ()->getCountry ();
           } 
           Mage::helper( 'marketplace/productranking' )->updateTotalSalesForAssignedProducts($getProductId,'+');
           /**
            * Check seller id has been set
            */
           if ($sellerId) {
               /**
                * Get order price,Get quantity ordered,Get payment method code
                */
               $productQty = $item->getQtyOrdered ();
               $orderPrice = $item->getPrice () * $item->getQtyOrdered ();


               if ($paymentCode == 'paypaladaptive') {
                   $credited = 1;
               } else {
                   $credited = 0;
               }
               $marketplace_shipping_description = Mage::getStoreConfig('carriers/apptha/title').' - '.Mage::getStoreConfig('carriers/apptha/name');
               if(Mage::getStoreConfig ( 'marketplace/shipping/shippingcost' ) && $shippingDescription == $marketplace_shipping_description ){
                   $shippingSellerShippingPrice = Mage::helper ( 'marketplace/market' )->getShippingPrice ( $sellerDefaultCountry, $shippingCountryIdentifier, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty );
               }else{
                   $shippingSellerShippingPrice = Mage::helper ( 'marketplace/market' )->getShippingPrice ( $sellerDefaultCountry, $shippingCountryIdentifier, $orderPrice, 0, 0, $productQty );
               }
               $sellerShippingPrice = Mage::helper ( 'marketplace/market' )->getSellerShippingPrice ( $sellerDefaultCountry, $shippingCountryIdentifier, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty );
               /**
                * Getting seller commission percent
                */
               $sellerCollection = Mage::helper ( 'marketplace/marketplace' )->getSellerCollection ( $sellerId );
               $percentPerProduct = Mage::helper ( 'marketplace/general' )->setSellerCommission ( $sellerCollection ['commission'] );
               $commissionFees = $orderPrice * ($percentPerProduct / 100);
               $sellerAmount = $shippingSellerShippingPrice;
               $sellerCommissionMode=Mage::getStoreConfig("marketplace/product/commission_mode");
               if($sellerCommissionMode=="category"){
                   $commissionFees=$this->getSellerCategoryCommission($getProductId,$orderPrice);
               }
               /**
                * Check the delivery schedule is enable status if it's enabled delivery details will added into seller information
                */
               $deliveryScheduleEnabled = Mage::getStoreConfig ( 'deliveryschedule/general/delivery_schedule_enabled' );
               if ($deliveryScheduleEnabled == 1 && $sellerShippingEnabled == 1 && ! in_array ( $sellerId, $sellerIdsArray )) {
                   /**
                    * Check the seller id exist or not
                    */
                   $sellerIdsArray [] = $sellerId;
                   $deliveryCost = $order->getShippingDeliveryCost ();
                   $sellerAmount = $deliveryCost + $sellerAmount;
               }
               /**
                * Subtract the commision fee from seller amount
                */
               $sellerAmount = $sellerAmount - $commissionFees;
               $orderEmailData = $this->orderDeliverySheduleEnabled($orderEmailData,$deliveryScheduleEnabled , $itemCount, $order);
               /**
                * Storing commission information in database table
                */
               if ($commissionFees > 0 || $sellerAmount > 0) {
                   $currencyCodeSymbol = Mage::helper('marketplace')->orderCurrencySymbol($order->getId ());
                   $currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
                   /**
                    * Compare the currency code for order product.
                    */
                   if($currencyCodeSymbol == $currencyCode){
                       $shippingAmount = round($sellerShippingPrice, 2);
                   }else{
                       $orderCurrencyCode = Mage::helper('marketplace')->orderCurrencyCode($order->getId ());
                       $currencyRates = Mage::getModel('directory/currency')->getCurrencyRates($currencyCode, array($orderCurrencyCode));
                       $shippingAmount = round(($currencyRates[$orderCurrencyCode] * $sellerShippingPrice), 2);
                   }
                   $productPrice = $orderPrice;
                   $sellerAmount = round(($productPrice + str_replace(',', '', $shippingAmount)), 2);
                   $totalSellerAmount = round((str_replace(',', '', $totalSellerAmount) + str_replace(',', '', $sellerAmount)), 2);
                   /**
                    * Store seller id in order email data
                    * and product quantity,product id,product amount,product commission fee, and seller amount,order id,customer email id
                    */
                   $orderEmailData [$itemCount] ['commission_fee'] = $commissionFees;
                   $orderEmailData [$itemCount] ['product_id'] = $getProductId;
                   $orderEmailData [$itemCount] ['product_amt'] = $productPrice;

                   $orderEmailData [$itemCount] ['seller_id'] = $sellerId;
                   $orderEmailData [$itemCount] ['product_qty'] = $productQty;

                   $orderEmailData [$itemCount] ['seller_amount'] = str_replace(',', '', $sellerAmount);
                   $orderEmailData [$itemCount] ['increment_id'] = $order->getIncrementId ();
                   $orderEmailData [$itemCount] ['customer_firstname'] = $order->getCustomerFirstname ();
                   $orderEmailData [$itemCount] ['customer_email'] = $order->getCustomerEmail ();
                   $itemCount = $itemCount + 1;
               }
           }

       }

       return $orderEmailData;


   }
   /**
    * Function to get order information
    *
    * @return array
    */
   public function orderEmailDataInformation(){

       $sellerDefaultCountry = '';
       /**
        * Create an array for hold on seller ids
        */
       $sellerIdsArray = array ();
       $nationalShippingPrice = $internationalShippingPrice = $totalSellerAmount = 0;
       /**
        * Get order collection.
        */
      $storeId = Mage::app()->getStore()->getId();
      if($storeId == 0){
           $orderIds = $this->getRequest ()->getParam ( 'order_id' );
      }else{
           $orderIds = $this->getRequest ()->getParam ( 'id' );
      }
       $order = Mage::getModel ( 'sales/order' )->load ( $orderIds );
       /**
        * Get order Shippment description.
        */
       $shippingDescription = $order->getShippingDescription();
       $itemCount = 0;
       $items = $order->getAllItems ();
       $shippingCountryId = '';
       $orderEmailData = array ();
       /**
        * Get all item from the order collection,Get product id,Get created At
        */
       foreach ( $items as $item ) {
           $shippingSellerPrice = $sellerShippingPrice = 0;
           $paymentMethodCode = $order->getPayment ()->getMethodInstance ()->getCode ();
           $getProductId = $item->getProductId ();
           $products = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $getProductId );
           $productType = $products->getTypeID ();
           $sellerId = $products->getSellerId ();
           /**
            * Get the shipping active status of seller
            */
           $sellerShippingEnabled = Mage::getStoreConfig ( 'carriers/apptha/active' );
           if ($sellerShippingEnabled == 1 && $productType == 'simple') {
               /**
                * Get the product national shipping price,and international shipping price,and shipping country
                */
               $internationalShippingPrice = $products->getInternationalShippingPrice ();
               $nationalShippingPrice = $products->getNationalShippingPrice ();

               $sellerDefaultCountry = $products->getDefaultCountry ();
               $shippingCountryId = $order->getShippingAddress ()->getCountry ();
           }
           Mage::helper( 'marketplace/productranking' )->updateTotalSalesForAssignedProducts($getProductId,'+');
           /**
            * Check seller id has been set
            */
           if ($sellerId) {
               /**
                * Get order price,Get quantity ordered,Get payment method code
                */
               $productQty = $item->getQtyOrdered ();
               $orderPrice = $item->getPrice () * $item->getQtyOrdered ();
               if ($paymentMethodCode == 'paypaladaptive') {
                   $credited = 1;
               } else {
                   $credited = 0;
               }
               $marketplace_shipping_description = Mage::getStoreConfig('carriers/apptha/title').' - '.Mage::getStoreConfig('carriers/apptha/name');
               if(Mage::getStoreConfig ( 'marketplace/shipping/shippingcost' ) && $shippingDescription == $marketplace_shipping_description ){
                   $shippingSellerPrice = Mage::helper ( 'marketplace/market' )->getShippingPrice ( $sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty );
               }else{
                   $shippingSellerPrice = Mage::helper ( 'marketplace/market' )->getShippingPrice ( $sellerDefaultCountry, $shippingCountryId, $orderPrice, 0, 0, $productQty );
               }
               $sellerShippingPrice = Mage::helper ( 'marketplace/market' )->getSellerShippingPrice ( $sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty );
               /**
                * Getting seller commission percent
                */
               $sellerCollection = Mage::helper ( 'marketplace/marketplace' )->getSellerCollection ( $sellerId );
               $percentPerProduct = Mage::helper ( 'marketplace/general' )->setSellerCommission ( $sellerCollection ['commission'] );
               $commissionFee = $orderPrice * ($percentPerProduct / 100);
               $sellerAmount = $shippingSellerPrice;
               $commissionMode=Mage::getStoreConfig("marketplace/product/commission_mode");
               if($commissionMode=="category"){
                   $commissionFee=$this->getSellerCategoryCommission($getProductId,$orderPrice);
               }
               /**
                * Check the delivery schedule is enable status if it's enabled delivery details will added into seller information
                */
               $deliveryScheduleEnable = Mage::getStoreConfig ( 'deliveryschedule/general/delivery_schedule_enabled' );
               if ($deliveryScheduleEnable == 1 && $sellerShippingEnabled == 1 && ! in_array ( $sellerId, $sellerIdsArray )) {
                   /**
                    * Check the seller id exist or not
                    */
                   $sellerIdsArray [] = $sellerId;
                   $deliveryCost = $order->getShippingDeliveryCost ();
                   $sellerAmount = $deliveryCost + $sellerAmount;
               }
               /**
                * Subtract the commision fee from seller amount
                */
               $sellerAmount = $sellerAmount - $commissionFee;
               $orderEmailData = $this->orderDeliverySheduleEnabled($orderEmailData,$deliveryScheduleEnable , $itemCount, $order);
               /**
                * Storing commission information in database table
                */
               if ($commissionFee > 0 || $sellerAmount > 0) {
                   $currencySymbol = Mage::helper('marketplace')->orderCurrencySymbol($order->getId ());
                   $currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
                   /**
                    * Compare the currency code for order product.
                    */
                   if($currencySymbol == $currencyCode){
                       $shippingAmount = round($sellerShippingPrice, 2);
                   }else{
                       $orderCurrencyCode = Mage::helper('marketplace')->orderCurrencyCode($order->getId ());
                       $currencyRates = Mage::getModel('directory/currency')->getCurrencyRates($currencyCode, array($orderCurrencyCode));
                       $shippingAmount = round(($currencyRates[$orderCurrencyCode] * $sellerShippingPrice), 2);
                   }
                   $productPrice = $orderPrice;
                   $sellerAmount = round(($productPrice + str_replace(',', '', $shippingAmount)), 2);
                   $totalSellerAmount = round((str_replace(',', '', $totalSellerAmount) + str_replace(',', '', $sellerAmount)), 2);
                   /**
                    * Store seller id in order email data
                    * and product quantity,product id,product amount,product commission fee, and seller amount,order id,customer email id
                    */
                   $orderEmailData [$itemCount] ['product_id'] = $getProductId;
                   $orderEmailData [$itemCount] ['product_amt'] = $productPrice;
                   $orderEmailData [$itemCount] ['seller_id'] = $sellerId;
                   $orderEmailData [$itemCount] ['product_qty'] = $productQty;
                   $orderEmailData [$itemCount] ['commission_fee'] = $commissionFee;
                   $orderEmailData [$itemCount] ['seller_amount'] = str_replace(',', '', $sellerAmount);
                   $orderEmailData [$itemCount] ['increment_id'] = $order->getIncrementId ();
                   $orderEmailData [$itemCount] ['customer_firstname'] = $order->getCustomerFirstname ();
                   $orderEmailData [$itemCount] ['customer_email'] = $order->getCustomerEmail ();
                   $itemCount = $itemCount + 1;
               }
           }

       }
       return $orderEmailData;
    }
   /**
    * Function to get category commission
    *
    * @return array
    */
   public function getSellerCategoryCommission($getProductId,$orderPrice){
       /**
        * load Product data
        * @var obj
        */
       $product = Mage::getModel('catalog/product')->load($getProductId);
       /**
        * Get category ids
        * @var unknown
        */
       $categories = $product->getCategoryIds();
       $commmax=array();
       /**
        * Increment foreach loop
        */
       foreach ($categories as $category_id) {
           /**
            * Load category data
            * @var int
            */
           $_cat = Mage::getModel('catalog/category')->load($category_id);
           $comm=$_cat->getCommission();
           /**
            * Checking whether it's empty array
            */
           if(isset($comm)){
               $commmax[]= $comm;
           }
       }
       if (isset($commmax)) {
           /**
            * Find maximum of commission
            * @var int
            */
           $percentperproductCommission = max($commmax);
       }
       else{
           /**
            * Get global commission
            * @var unknown
            */
           $percentperproductCommission =Mage::getStoreConfig("marketplace/marketplace/percentperproduct");
       }
       $percentperproductCommission=(int)$percentperproductCommission;
       return $orderPrice * ($percentperproductCommission / 100);
   }
   /**
    * Function to get order delivery schedule information
    *
    * @return array
    */

   public function orderDeliverySheduleEnabled ( $orderEmailData,$deliveryScheduleEnable , $itemCount, $order) {
       if ($deliveryScheduleEnable == 1) {
           /**
            * Get shipping delivery cost
            * Get shipping delivery comments
            * Get shipping delivery date
            * Get shipping delivery schedule
            * Get shipping delivery time
            */
           $deliveryCost = $order->getShippingDeliveryCost ();
           $deliveryComment = $order->getShippingDeliveryComments ();
           $deliveryTypeInfo = $order->getShippingDeliverySchedule ();
           $deliveryTimeInfo = $order->getShippingDeliveryTime ();
           $deliveryDateInfo = $order->getShippingDeliveryDate ();
           $orderEmailData [$itemCount] ['delivery_date'] = $deliveryDateInfo;
           $orderEmailData [$itemCount] ['delivery_cost'] = $deliveryCost;
           $orderEmailData [$itemCount] ['delivery_comment'] = $deliveryComment;

           $orderEmailData [$itemCount] ['delivery_type'] = $deliveryTypeInfo;
           $orderEmailData [$itemCount] ['delivery_time'] = $deliveryTimeInfo;
       }
       return $orderEmailData;
   }
   /**
    * Function to get seller store commission data
    *
    * @return array
    */
   public function sellerStoreCommissionData($commissionDataArr) {
       /**
        * Get Marketplace Commission
        * @var object
        */
       $model = Mage::getModel ( 'marketplace/commission' );
       $model->setData ( $commissionDataArr );
       /**
        * Save data
        */
       $model->save ();
       return $model->getId ();
   }
   /**
    * Function to get seller group identifier
    *
    * @return array
    */
   public function getSellerGroupId(){
       return Mage::helper ( 'marketplace' )->getGroupId ();
   }
}
