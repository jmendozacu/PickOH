<?php

class Magecheckout_SocialLogin_Block_Adminhtml_System_Config_Form_Field_Redirecturl extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html_id     = $element->getHtmlId();
        $redirectUrl = $this->_redirectUrl($element);
        $redirectUrl = str_replace('index.php/', '', $redirectUrl);
        $html        = '<input readonly id="' . $html_id . '" class="input-text" value="' . $redirectUrl . '" onclick="this.select()">';

        return $html;
    }

    /**
     * Add Redirect Uri config
     * @param $element
     * @return mixed|string
     */
    protected function _redirectUrl($element)
    {
        $htmlId = $element->getHtmlId();
        switch ($htmlId) {
            case 'sociallogin_facebook_redirect_url':
                return Mage::helper('sociallogin/facebook')->getAuthUrl();
            case 'sociallogin_google_redirect_url':
                return Mage::helper('sociallogin/google')->getAuthUrl();
            case 'sociallogin_instagram_redirect_url':
                return Mage::helper('sociallogin/instagram')->getAuthUrl();
            case 'sociallogin_linkedin_redirect_url':
                return Mage::helper('sociallogin/linkedin')->getAuthUrl();

            case 'sociallogin_twitter_redirect_url':
                return Mage::helper('sociallogin/twitter')->getAuthUrl();

            default:
                return '';
        }
    }
}
