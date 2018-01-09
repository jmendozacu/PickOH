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
 * This file is used to Customer login, Customer registeration, Seller login and Seller registeration functionality
 *
 * In this class, login/create account and forget password operations.
 * Also it will connects social networks such as Google, Twitter, Yahoo and Facebook oAuth connections.
 */
class Apptha_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Render Apptha sociallogin pop-up layout
     * @return void
     */
    public function indexAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Customer Register Action
     *
     * @return string
     */
    public function customerAction($firstname, $lastname, $email, $provider, $data) {
        $customer = Mage::getModel ( 'customer/customer' );
        $collection = $customer->getCollection ();
        $adminApproval = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/need_approval' );
        $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
        if ($customer->getSharingConfig ()->isWebsiteScope ()) {
            $collection->addAttributeToFilter ( 'website_id', Mage::app ()->getWebsite ()->getId () );
        }
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $collection->addFieldToFilter ( 'entity_id', array (
                    'neq' => $this->_getCustomerSession ()->getCustomerId ()
            ) );
        }
        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
        $standardInfo ['email'] = $email;
        $standardInfo ['first_name'] = $firstname;
        $standardInfo ['last_name'] = $lastname;
        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $standardInfo ['email'] );
        if ($customer->getId ()) {
            $customerGroupId = $customer->getGroupId ();
            $loggedCustomerStatus = $customer->getCustomerstatus ();
            if ($loggedCustomerStatus == 1) {
                $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );
                $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Your account has been successfully connected through' . ' ' . $provider ) );
            }

            if ($customerGroupId == $groupId && ($loggedCustomerStatus == 0 || $loggedCustomerStatus == 2)) {
                $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
            }
            $link = Mage::getSingleton ( 'customer/session' )->getLink ();
            if (! empty ( $link )) {
                $requestPath = trim ( $link, '/' );
            }
            /**
             * Check if customer current URL is checkout URL.
             */
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect ( $requestPath );
                return;
            } else {
                $enableRedirectStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );
                if ($enableRedirectStatus) {
                    ($customerGroupId == $groupId && $loggedCustomerStatus == 1)?$redirect = Mage::getUrl ( 'marketplace/seller/dashboard' ):$redirect = $this->_loginPostRedirect ();
                } else {
                    $redirect = Mage::getSingleton ( 'core/session' )->getReLink ();
                }
                $this->_redirectUrl ( $redirect );
                return;
            }
        }
        $randomPassword = $customer->generatePassword ( 8 );
        $customer->setId ( null )->setSkipConfirmationIfEmail ( $standardInfo ['email'] )->setFirstname ( $standardInfo ['first_name'] )->setLastname ( $standardInfo ['last_name'] )->setEmail ( $standardInfo ['email'] )->setPassword ( $randomPassword )->setConfirmation ( $randomPassword )->setLoginProvider ( $provider );
        $customer=Mage::helper('sociallogin')->getSellerData($adminApproval,$data,$customer,$groupId);
        if ($this->getRequest ()->getParam ( 'is_subscribed', false )) {
            $customer->setIsSubscribed ( 1 );
        }
        $errors = array ();
        $validationCustomer = $customer->validate ();
        $errors=Mage::helper('sociallogin')->getValidateData($validationCustomer,$errors);
        $validationResult = true;
        $customerConfirmation = $customer->isConfirmationRequired();
        if (true === $validationResult) {
            $customerId = $customer->save ()->getId ();
            $this->getStatus ( $customerId, $customer, $adminApproval, $data, $customerConfirmation );
        } else {
            $this->_getCustomerSession ()->setCustomerFormData ( $customer->getData () );
            $this->_getCustomerSession ()->addError ( $this->__ ( 'User profile can\'t provide all required info, please register and then connect with Apptha Social login.' ) );
            if (is_array ( $errors )) {
                foreach ( $errors as $errorMessage ) {
                    $this->_getCustomerSession ()->addError ( $errorMessage );
                }
            }
            $this->_redirect ( 'customer/account/create' );
            return;
        }
    }

    /**
     * Retrieve customer session from core customer session
     *
     * @return array
     */
    private function _getCustomerSession() {
        return Mage::getSingleton ( 'customer/session' );
    }

    /**
     * Redirect customer dashboard URL after logging in
     *
     * @return string URL
     */
    protected function _loginPostRedirect($sellerLogin) {
        $session = $this->_getCustomerSession ();

        if (! $session->getBeforeAuthUrl () || $session->getBeforeAuthUrl () == Mage::getBaseUrl ()) {
            /**
             * Set Default Account URL to customer session
             */
            if ($sellerLogin == 1) {
                $session->setBeforeAuthUrl ( Mage::helper ( 'marketplace/marketplace' )->dashboardUrl () );
            } else {
                $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getAccountUrl () );
            }
            /**
             * Redirect customer to the last page visited after logging in
             */
            if ($session->isLoggedIn ()) {
                if (! Mage::getStoreConfigFlag ( 'customer/startup/redirect_dashboard' ) && $referer = $this->getRequest ()->getParam ( Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME )) {

                    $referer = Mage::helper ( 'core' )->urlDecode ( $referer );
                    if ($this->_isUrlInternal ( $referer )) {
                        $session->setBeforeAuthUrl ( $referer );
                    }
                }
                /**
                 * Get the authendication url for the customer.
                 * Set before authendication url.
                 */
                if ($session->getAfterAuthUrl ()) {
                    $session->setBeforeAuthUrl ( $session->getAfterAuthUrl ( true ) );
                }
            } else {
                $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            }
        } else if ($session->getBeforeAuthUrl () == Mage::helper ( 'customer' )->getLogoutUrl ()) {
            $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getDashboardUrl () );
        } else {
            if (! $session->getAfterAuthUrl ()) {
                $session->setAfterAuthUrl ( $session->getBeforeAuthUrl () );
            }
            /**
             * Check customer is loggin or not.
             */
            if ($session->isLoggedIn ()) {
                $session->setBeforeAuthUrl ( $session->getAfterAuthUrl ( true ) );
            }
        }

        return $session->getBeforeAuthUrl ( true );
    }

    /**
     * @Twitter login action
     */
    public function twitterloginAction() {

        /**
         * Include Twitter files for oAuth connection
         */
        require 'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';

        /**
         * Retrives @Twitter consumer key and secret key from core session
         */
        $twOauthToken = Mage::getSingleton ( 'customer/session' )->getTwToken ();
        $twOauthTokenSecret = Mage::getSingleton ( 'customer/session' )->getTwSecret ();
        $twitteroauth = new TwitterOAuth ( YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $twOauthToken, $twOauthTokenSecret );

        /**
         * Get Accesss token from @Twitter oAuth
         */
        $oauthVerifier = $this->getRequest ()->getParam ( 'oauth_verifier' );
        $twitteroauth->getAccessToken ( $oauthVerifier );

        /**
         * Get @Twitter User Details from twitter account
         *
         * @return string Redirect URL or Customer save action
         */
        $userInfo = $twitteroauth->get ( 'account/verify_credentials' );
        $data = $this->getRequest ()->getParam ( 'fb' );
        if (isset ( $userInfo->error )) {
            Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Twitter Login connection failed' ) );
            $url = Mage::helper ( 'customer' )->getAccountUrl ();
            return $this->_redirectUrl ( $url );
        } else {
            $firstName = $userInfo->name;
            $userInfo->id;
            /**
             * Get the registered email in the twitter.
             */
            $email = Mage::getSingleton ( 'customer/session' )->getTwemail ();
            $lastName = ' ';
            if ($email == '' || $firstName == '') {
                /**
                 * Throw the error message if connection failed with the twitter account.
                 */
                Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Twitter Login connection failed' ) );
                $url = Mage::helper ( 'customer' )->getAccountUrl ();
                return $this->_redirectUrl ( $url );
            } else {
                /**
                 * If registration is success useing twitter email send to customer action controller.
                 */
                $this->customerAction ( $firstName, $lastName, $email, 'Twitter', $data );
            }
        }
    }

    /**
     * @Twitter post action
     *
     * @return string Returns Twitter page URL for Authentication
     */
    public function twitterpostAction() {
        $provider = '';
        $twitterEmail = ( string ) $this->getRequest ()->getPost ( 'email_value' );
        $data = $this->getRequest ()->getPost ( 'twitter_hidden' );
        Mage::getSingleton ( 'customer/session' )->setTwemail ( $twitterEmail );
        $customer = Mage::getModel ( 'customer/customer' );
        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $twitterEmail );
        /**
         * Get customer id to get customer information.
         */
        $customerIdByEmail = $customer->getId ();
        $customer = Mage::getModel ( 'customer/customer' )->load ( $customerIdByEmail );
        $googleUID = $customer->getGoogleUid ();
        /**
         * Check google id to get customer information.
         */
        if ($googleUID != '') {
            $provider .= ' Google';
        }
        /**
         * Check facebook id to get customer information.
         */
        $facebookUID = $customer->getFacebookUid ();
        if ($facebookUID != '') {
            $provider .= ', Facebook';
        }
        /**
         * Check linkedin id to get customer information
         */
        $linkedinUID = $customer->getLinkedinUid ();
        if ($linkedinUID != '') {
            $provider .= ', Linkedin';
        }
        /**
         * Check yahoo user id to get customer information
         */
        $yahooUId = $customer->getYahooUid ();
        if ($yahooUId != '') {
            $provider .= ', Yahoo';
        }
        /**
         * Get twitter uid
         */
       $provider = ltrim ( $provider, ',' );
        /**
         * If customer id is empty.
         * Checked to get customer id from social login.
         */

            $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ( $data );
            $this->getResponse ()->setBody ( $url );

    }

    /**
     * @facebook login action
     *
     * Connect facebook Using oAuth coonection.
     *
     * @return string redirect URL
     *
     */
    public function fbloginAction() {
        if ($this->getRequest ()->getParam ( 'email' )) {
            $email = $this->getRequest ()->getParam ( 'email' );
            $firstName = $this->getRequest ()->getParam ( 'fname' );
            $lastName = $this->getRequest ()->getParam ( 'lname' );
            $data = $this->getRequest ()->getParam ( 'fb' );
            $this->customerAction ( $firstName, $lastName, $email, 'Facebook', $data );
        } else {
            Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Facebook Login connection failed' ) );
        }
        $url = Mage::helper ( 'customer' )->getAccountUrl ();
        return $this->_redirectUrl ( $url );
    }

    /**
     * @Google login action
     *
     * Connect Google Using oAuth coonection.
     *
     * @return string redirect URL either customer save and loggedin or an error if any occurs
     */
    public function googlepostAction() {
        $error = $this->getRequest ()->getParam ( 'error' );
        if ($error) {
            return $this->_redirectUrl ( Mage::getBaseUrl () );
        }
        /**
         * Include @Google library files for oAuth connection
         */
        require_once 'sociallogin/src/Google_Client.php';
        require_once 'sociallogin/src/contrib/Google_Oauth2Service.php';
        /**
         * Retrieves the @google_client_id, @google_client_secret
         */
        $googleClientId = Mage::getStoreConfig ( 'sociallogin/google/google_id' );
        $googleClientSecret = Mage::getStoreConfig ( 'sociallogin/google/google_secret' );
        $googleDeveloperKey = Mage::getStoreConfig ( 'sociallogin/google/google_develop' );
        $googleRedirectUrl = Mage::getUrl () . 'sociallogin/index/googlepost/';
        $gClient = new Google_Client ();
        /**
         * Set application name
         * set client id
         * ser client secret key
         * set redirect url
         * set developer key
         */
        $gClient->setApplicationName ( 'login' );
        $gClient->setClientId ( $googleClientId );
        $gClient->setClientSecret ( $googleClientSecret );
        $gClient->setRedirectUri ( $googleRedirectUrl );
        $gClient->setDeveloperKey ( $googleDeveloperKey );
        $googleOauthV2 = new Google_Oauth2Service ( $gClient );
        $token = Mage::getSingleton ( 'core/session' )->getGoogleToken ();
        $reset = $this->getRequest ()->getParam ( 'reset' );
        $data = $this->getRequest ()->getParam ( 'fb' );
        if ($reset) {
            unset ( $token );
            $gClient->revokeToken ();
            $this->_redirectUrl ( filter_var ( $googleRedirectUrl, FILTER_SANITIZE_URL ) );
        }

        $code = $this->getRequest ()->getParam ( 'code' );

        if (isset ( $code )) {
            /**
             * Get information from third party.
             */
            $gClient->authenticate ( $code );
            Mage::getSingleton ( 'core/session' )->setGoogleToken ( $gClient->getAccessToken () );
            $this->_redirectUrl ( filter_var ( $googleRedirectUrl, FILTER_SANITIZE_URL ) );
            $this->_redirectUrl ( $googleRedirectUrl );
            return;
        }

        if (isset ( $token )) {
            $gClient->setAccessToken ( $token );
        }
        if ($gClient->getAccessToken ()) {
            /**
             * Retrieve user details If user succesfully in Google
             */
            $user = $googleOauthV2->userinfo->get ();
            $user ['id'];
            filter_var ( $user ['name'], FILTER_SANITIZE_SPECIAL_CHARS );
            $email = filter_var ( $user ['email'], FILTER_SANITIZE_EMAIL );
            filter_var ( $user ['link'], FILTER_VALIDATE_URL );
            $token = $gClient->getAccessToken ();
            Mage::getSingleton ( 'core/session' )->setGoogleToken ( $token );
        } else {
            /**
             * get google google Authendication URL
             */
            $authUrl = $gClient->createAuthUrl ();
        }
        /**
         * If user doesn't logged-in redirects the login URL
         */
        if (isset ( $authUrl )) {
            $this->_redirectUrl ( $authUrl );
        } else {
            /**
             * Fetching user infor from an array, @param array $user
             *
             * @param string $given_name,
             *            $familyname, $email general info for users from @google account.
             */
            $firstName = $user ['given_name'];
            $lastName = $user ['family_name'];

            $email = $user ['email'];
            $user ['id'];

            if ($email == '') {
                Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Google Login connection failed' ) );
                $url = Mage::helper ( 'customer' )->getAccountUrl ();
                return $this->_redirectUrl ( $url );
            } else {

                $this->customerAction ( $firstName, $lastName, $email, 'Google', $data );
            }
        }
    }



    /**
     * Customer Login layout render Action
     *
     * Rendering the layout if social login extension is enabled
     */
    public function loginAction() {
        /**
         * check that already customer is logged in or not.
         */
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
            return;
        }
        /**
         * Check the social login configuration is activation status.
         */
        if (Mage::getStoreConfig ( 'sociallogin/general/enable_sociallogin' ) == 1) {
            return;
        }
        $this->getResponse ()->setHeader ( 'Login-Required', 'true' );
        /**
         * Render layout for customer
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->_initLayoutMessages ( 'catalog/session' );
        $this->renderLayout ();
    }

    /**
     * Customer Create Account layout render Action
     *
     * Rendering the layout if social login extension is enabled
     */
    public function createAction() {
        /**
         * Check that customer is logged in or not.
         */
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
            return;
        } else {
            /**
             * Check that social login configuration activation status.
             */
            $enableStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_sociallogin' );
            if ($enableStatus == 1) {
                return;
            }
        }

        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->renderLayout ();
    }

    /**
     * Validation for Tax/Vat field for current store
     */
    public function _isVatValidationEnabled($store = null) {
        return Mage::helper ( 'customer/address' )->isVatValidationEnabled ( $store );
    }

    /**
     * Customer welcome function
     *
     * Its used for print welcome message once successfully logged in
     *
     * @return string customer success page URL.
     */
    public function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {

        $loginProvider = $customer->getLoginProvider();
        if(!isset($loginProvider)){
        $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Thank you for registering with %s.', Mage::app ()->getStore ()->getFrontendName () ) );
        }
        /**
         * Send email for the newly registered users.
         */
        $customer->sendNewAccountEmail ( $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app ()->getStore ()->getId () );

        $successUrl = Mage::getUrl ( 'customer/account', array (
                '_secure' => true
        ) );

        /**
         * Get the authendication url for the customer.
         */
        if ($this->_getCustomerSession ()->getBeforeAuthUrl ()) {
            $successUrl = $this->_getCustomerSession ()->getBeforeAuthUrl ( true );
        }
        return $successUrl;
    }

    /**
     * Customer login Action
     *
     * validate the social login form posted values if the user is registered user or not
     *
     * @return string Redirect URL.
     */
    public function customerloginpostAction() {
        $session = $this->_getCustomerSession ();
        $sellerLogin = $this->getRequest ()->getParam ( 'login_hidden' );
        $login ['username'] = $this->getRequest ()->getParam ( 'email' );
        $login ['password'] = $this->getRequest ()->getParam ( 'password' );
        $errorFlag=$groupIderrorFlag=$statuserrorFlag=false;
        $errorFlag=$this->getSellerResponse($session);
        if ($this->getRequest ()->isPost () && ! empty ( $login ['username'] ) && ! empty ( $login ['password'] )) {
            try {
                if ($sellerLogin == 1) {
                    $customer = Mage::getModel ( 'customer/customer' );
                    $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $login ['username'] );
                    $customerGroupId = $customer->getGroupId ();
                    $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
                    $groupIderrorFlag=$this->checkGroupId($customerGroupId,$groupId);
                    $customerStatus = $customer->getCustomerstatus ();
                    $statuserrorFlag=$this->checkCustomerStatus($customerStatus);
                    if($errorFlag ||$statuserrorFlag||$groupIderrorFlag){
                        return ;
                    }
                }
                $session->login ( $login ['username'], $login ['password'] );
                if ($session->getCustomer ()->getIsJustConfirmed ()) {
                    $this->getResponse ()->setBody ( $this->_welcomeCustomer ( $session->getCustomer (), true ) );
                }
            } catch ( Mage_Core_Exception $e ) {
                switch ($e->getCode ()) {
                    case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED :
                        $value = Mage::helper ( 'customer' )->getEmailConfirmationUrl ( $login ['username'] );
                        $message = Mage::helper ( 'customer' )->__ ( 'Account Not Confirmed', $value );
                        $this->getResponse ()->setBody ( $message );
                        break;
                    case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD :
                        $message = $this->__ ( 'Invalid Email Address or Password' );
                        $this->getResponse ()->setBody ( $message );
                        break;
                    default :
                        $message = $e->getMessage ();
                        $this->getResponse ()->setBody ( $message );
                }
                $session->setUsername ( $login ['username'] );
            } catch ( Exception $e ) {
                return $e;
            }
            if ($session->getCustomer ()->getId ()) {
                $link = Mage::getSingleton ( 'customer/session' )->getLink ();
                $requestPath = '';
                $requestPath=Mage::helper('sociallogin')->getExplodedLink($link);
                if ($requestPath == 'checkout/onestep/' || $requestPath == 'checkout/cart/') {
                    $this->getResponse ()->setBody ( Mage::getSingleton ( 'core/session' )->getReLink () );
                } else {
                    $enableRedirectStatus = 0;
                    $enableRedirectStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );
                    if ($enableRedirectStatus == 1) {
                        $this->getResponse ()->setBody ( $this->_loginPostRedirect ( $sellerLogin ) );
                    } else {
                        $this->getResponse ()->setBody ( $_SERVER['HTTP_REFERER'] );
                    }
                }
            }
        }
    }

    /**
     * Customer Register Action
     *
     * validate the social regiter form posted values
     *
     * @return string Redirect URL.
     */
       public function createPostAction() {
        $errorFlag= $confirmationFlag= $exceptionFlag=false;
        $adminApproval = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/need_approval' );
        $sellerRegisteration = $this->getRequest ()->getPost ( 'register_hidden' );
        $customer = Mage::getModel ( 'customer/customer' );
        $session = $this->_getCustomerSession ();
        $session->isLoggedIn ()?$this->_redirect ( '*/*/' ):null;
        $session->setEscapeMessages ( true );
        if ($this->getRequest ()->isPost ()) {
            $errors = array ();
             $customer=Mage::helper('sociallogin')-> checkCurrentCustomer($customer);
            $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
            $customer=Mage::helper('sociallogin')->getSellerData($adminApproval,$sellerRegisteration,$customer,$groupId);
            $customerForm = Mage::getModel ( 'customer/form' );
            $customerForm->setFormCode ( 'customer_account_create' )->setEntity ( $customer );
            $customerData = $customerForm->extractData ( $this->getRequest () );
           $isSubscribed= $this->getRequest ()->getParam ( 'is_subscribed', false );
            Mage::helper('sociallogin')->checkSubscribed($customer,$isSubscribed);
            $customer->getGroupId ();
            try {
                $customerErrors = $customerForm->validateData ( $customerData );
                $errors = ($customerErrors !== true)? array_merge ( $customerErrors, $errors ):$errors;
                if ($customerErrors)  {
                    $customerForm->compactData ( $customerData );
                    $customer->setPassword ( $this->getRequest ()->getPost ('password') );
                    $magentoVersion = Mage::getVersion ();
                    $confirmation=$this->getRequest ()->getPost ( 'confirmation' );
                    Mage::helper('sociallogin')->checkConfirmation($magentoVersion,$confirmation,$customer);
                    $customerErrors = $customer->validate ();
                    $errors=Mage::helper('sociallogin')->checkAddressErrors($errors,$customerErrors);
                }
                $validationResult = count ( $errors ) == 0;
                if (true === $validationResult) {
                    $customerId = $customer->save ()->getId ();
                    $confirmationFlag=Mage::helper('sociallogin')->setSellerSignup($customer,$adminApproval,$sellerRegisteration,$customerId,$session);
                    if ($adminApproval == 1 && $sellerRegisteration == 1) {
                        Mage::helper('sociallogin/system')->getSellerProfileModel ()->adminApproval ( $customerId );
                        Mage::dispatchEvent ('customer_register_success', array ('account_controller' => $this,'customer'  => $customer ) );
                        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ('Admin Approval is required. Please wait until admin confirms your Seller Account') );
                        $confirmationFlag=true;
                    } else {
                       Mage::helper('sociallogin')->sendSignUpMail($sellerRegisteration,$adminApproval,$customerId);
                        $this->setSellerRegistration($sellerRegisteration,$customerId);
                        Mage::dispatchEvent('customer_register_success', array ('customer_register_success'=> $this,'customer'  => $customer ));
                        $session->setCustomerAsLoggedIn ( $customer );
                        $session->renewSession ();
                        $this->getResponse ()->setBody ( $this->_welcomeCustomer ( $customer ) );
                    }
                    isset($confirmationFlag)?$this->getResponse()->setBody(Mage::getUrl('/index', array('_secure' => true))):null;
                    } else {
                        $session->setCustomerFormData ( $this->getRequest ()->getPost () );
                        if (is_array ( $errors )) {
                            $session->$errors[0];
                            $this->getResponse ()->setBody ( $errors[0] );
                            $errorFlag=true;
                        } else {
                            $session->addError ( $this->__ ( 'Invalid customer data' ) );
                        }
                    }
            } catch ( Mage_Core_Exception $e ) {
               $session->setCustomerFormData ( $this->getRequest ()->getPost () );
                if ($e->getCode () === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $message = $this->__ ( 'Email already exists' );
                    $this->getResponse ()->setBody ( $message );
                    $session->setEscapeMessages ( false );
                    return;
                } else {
                    $message = $e->getMessage ();
                    $this->getResponse ()->setBody ( $message );
                    return;
                }
                $session->addError ( $message );
            } catch ( Exception $e ) {
                $session->setCustomerFormData ( $this->getRequest ()->getPost () )->addException ( $e, $this->__ ( 'Cannot save the customer.' ) );
            }

            if($errorFlag || $confirmationFlag|| $exceptionFlag){
                return;
            }
        }
        $this->getResponse ()->setBody ( $message );
        (! Mage::getStoreConfigFlag ( 'customer/startup/redirect_dashboard' ))?($this->getResponse ()->setBody ( Mage::getUrl('/index', array('_secure' => true)))):($this->getResponse()->setBody(Mage::helper ( 'customer' )->getAccountUrl()));
    }

    /**
     * Function to set seller registration
     * @return void
     *
     */
    public function setSellerRegistration($sellerRegisteration,$customerId){
        if ($sellerRegisteration == 1) {
            Mage::getModel ( 'marketplace/sellerprofile' )->newSeller ( $customerId );
        }
    }
    /**
     * ForgetPassword Action
     * @param string $email
     * @return string $message.
     */
    public function forgotPasswordPostAction() {
        $email = ( string ) $this->getRequest ()->getParam ( 'forget_password' );
        $customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
        if ($customer->getId ()) {
            try {
                $newResetPasswordLinkToken = Mage::helper ( 'customer' )->generateResetPasswordLinkToken ();
                $customer->changeResetPasswordLinkToken ( $newResetPasswordLinkToken );
                $customer->sendPasswordResetConfirmationEmail ();
            } catch ( Exception $exception ) {
                $this->_getCustomerSession ()->addError ( $exception->getMessage () );
                return;
            }
            $message = $this->__ ( ' you will receive an email (' . $email . ') with a link to reset your password' );
        } else {
            $message = $this->__ ( 'There is no account associated with this email-id' ) . '.';
        }

        $this->getResponse ()->setBody ( $message );
    }
    /**
     * Get the customer status.
     *
     * @param customerId,customer data.
     * @return void
     */
    public function getStatus($customerId, $customer, $adminApproval, $data,$customerConfirmation) {
        $session = $this->_getCustomerSession ();

        if ($customerConfirmation && $adminApproval == 1 && $data == 1) {

            $customer->sendNewAccountEmail ( 'confirmation', $session->getBeforeAuthUrl (), Mage::app ()->getStore ()->getId () );
            $customerId = $customer->getId ();
            Mage::getModel ( 'marketplace/sellerprofile' )->adminApproval ( $customerId );
            /**
             * If user directly login to the site, we can display error message for account confirmation link sent to email.
             */
            $session->addSuccess ( $this->__ ( 'Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper ( 'customer' )->getEmailConfirmationUrl ( $customer->getEmail () ) ) );
            $this->getResponse ()->setBody ( Mage::getUrl ( '/index', array (
                    '_secure' => true
            ) ) );
        }
       else if ($adminApproval == 1 && $data == 1) {
            Mage::getModel ( 'marketplace/sellerprofile' )->adminApproval ( $customerId );
            Mage::dispatchEvent ( 'customer_register_success', array (
                    'account_controller' => $this,
                    'customer' => $customer
            ) );

            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
            $redirecturl = $this->getResponse ()->setBody ( Mage::getUrl ( '/index', array (
                    '_secure' => true
            ) ) );
            $this->_redirectUrl ( $redirecturl );
            return;
        } else {
            $session = $this->_getCustomerSession ();
            Mage::getModel ( 'marketplace/sellerprofile' )->newSeller ( $customerId );
            Mage::dispatchEvent ( 'customer_register_success', array (
                    'account_controller' => $this,
                    'customer' => $customer
            ) );
            $session->setCustomerAsLoggedIn ( $customer );
            $session->renewSession ();
            $redirecturl = $this->getResponse ()->setBody ( $this->_welcomeCustomer ( $customer ) );
            $this->_redirectUrl ( $redirecturl );
            $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Thank you for registering with %s', Mage::app ()->getStore ()->getFrontendName () ) . '. ' . $this->__ ( 'You will receive welcome email with registration info in a moment.' ) );
            $customer->sendNewAccountEmail ();
            $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );
            $link = Mage::getSingleton ( 'customer/session' )->getLink ();
            $requestPath=Mage::helper('sociallogin')->getLink($link);
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect ( $requestPath );
                return;
            } else {
                $enableRedirectStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );
               ($enableRedirectStatus)?
               $redirect = $this->_loginPostRedirect ():$redirect = Mage::getSingleton ( 'core/session' )->getReLink ();
                $this->_redirectUrl ( $redirect );
                return;
            }
        }
    }
    /**
     * Function  to get session response
     * @return object
     */
    public function getSellerResponse($session){
        if ($session->isLoggedIn()) {
            $message = 'Already loggedin';
            $this->getResponse ()->setBody ( $message );
            return true;
        }
    }
    /**
     * Function to check group id
     * @return object
     */
    public function checkGroupId($customerGroupId,$groupId){
        if ($customerGroupId != $groupId) {
            $this->getResponse ()->setBody ( 'You must have a Seller Account to access this page' );
            return true;
        }
    }
    /**
     * Function to check customer status
     * @return object
     */
    public function checkCustomerStatus($customerStatus){
        if ($customerStatus == 2 || $customerStatus == 0) {
            /**
             * If seller is logged in before admin approval, display the error message.
             */
            $this->getResponse ()->setBody ( 'Admin Approval is required for Seller Account' );
            return true;
        }
    }
}