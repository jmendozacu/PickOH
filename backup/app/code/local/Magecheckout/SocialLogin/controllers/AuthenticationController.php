<?php

class Magecheckout_SocialLogin_AuthenticationController extends Mage_Core_Controller_Front_Action
{
    /**
     * @param $name
     * @return mixed
     */
    public function getHelperApi($name)
    {
        return Mage::helper('sociallogin/' . $name);
    }

    /**
     * get Customer by email
     *
     * @param      $email
     * @param null $websiteId
     * @return mixed
     */
    public function getCustomerByEmailAction($email, $websiteId = null)
    {
        $websiteId = $websiteId ? $websiteId : Mage::app()->getStore()->getWebsiteId();
        $customer  = Mage::helper('sociallogin')->getCustomerByEmail($email, $websiteId);

        return $customer;
    }


    /**
     * Create customer account from api response data
     *
     * @param      $data
     * @param null $websiteId
     * @param null $storeId
     * @return mixed
     */
    public function createCustomerAccount($data, $websiteId = null, $storeId = null)
    {
        $websiteId = $websiteId ? $websiteId : Mage::app()->getStore()->getWebsiteId();
        $storeId   = $storeId ? $storeId : Mage::app()->getStore()->getStoreId();
        $customer  = Mage::helper('sociallogin')->createCustomerAccount($data, $websiteId, $storeId);
        if ($customer->getConfirmation()) {
            try {
                $customer->setConfirmation(null);
                $customer->save();
            } catch (Exception $e) {
                Mage::log($e->getMessage());
            }
        }

        return $customer;
    }

    /**
     * Append string to current page
     *
     * @param $string
     */
    protected function _appendJsToHead($string)
    {
        $this->loadLayout();
        $layout = Mage::app()->getLayout();
        $block  = $layout->createBlock('core/text');
        $block->setText(
            "<script type='text/javascript'>$string</script>"
        );
        $this->getLayout()->getBlock('head')->append($block);
        $this->renderLayout();
    }

    /**
     * redirect login
     *
     * @return mixed
     */
    protected function  _loginPostRedirect()
    {
        $session = Mage::getSingleton('customer/session');

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }

        return $session->getBeforeAuthUrl(true);
    }

    /**
     * @param $message
     */
    public function addSuccess($message)
    {
        Mage::getSingleton('core/session')->addSuccess($message);
    }

    /**
     * @param $message
     */
    public function addError($message)
    {
        Mage::getSingleton('core/session')->addError($message);
    }

    public function addNotice($message)
    {
        Mage::getSingleton('core/session')->addNotice($message);
    }

    /**
     * get Customer Session
     *
     * @return Mage_Customer_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Create row in author table if not exist customer email from response api
     *
     * @param $customerId
     * @param $tokenId
     * @param $type
     * @return bool
     */
    public function createAuthor($tokenId, $customerId, $type)
    {
        $authorModel = Mage::getModel('sociallogin/author');
        $data        = array(
            'token_id'    => $tokenId,
            'customer_id' => $customerId,
            'type'        => $type,
        );
        $authorModel->setData($data);
        try {
            $authorModel->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * get Customer by token id
     *
     * @param $tokenId
     * @param $socialType
     */
    public function getAuthorByTokenId($tokenId, $socialType)
    {

        $author = Mage::getModel('sociallogin/author')->getCollection()
            ->addFieldToFilter('token_id', $tokenId)
            ->addFieldToFilter('type', $socialType)
            ->getFirstItem();

        return $author;
    }

    /**
     * get Customer Edit Url
     *
     * @return string
     */
    public function getCustomerEditUrl()
    {
        return Mage::getUrl('customer/account/edit', array('_secure' => true));
    }
}