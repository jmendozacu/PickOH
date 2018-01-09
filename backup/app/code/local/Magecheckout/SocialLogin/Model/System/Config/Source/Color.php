<?php

class Magecheckout_SocialLogin_Model_System_Config_Source_Color
{
    public function toOptionArray()
    {
        return array(
            array('value' => '#3399cc', 'label' => Mage::helper('sociallogin')->__('Default')),
            array('value' => 'orange', 'label' => Mage::helper('sociallogin')->__('Orange')),
            array('value' => 'green', 'label' => Mage::helper('sociallogin')->__('Green')),
            array('value' => 'black', 'label' => Mage::helper('sociallogin')->__('Black')),
            array('value' => 'blue', 'label' => Mage::helper('sociallogin')->__('Blue')),
            array('value' => 'darkblue', 'label' => Mage::helper('sociallogin')->__('Dark Blue')),
            array('value' => 'pink', 'label' => Mage::helper('sociallogin')->__('Pink')),
            array('value' => 'red', 'label' => Mage::helper('sociallogin')->__('Red')),
            array('value' => 'violet', 'label' => Mage::helper('sociallogin')->__('Violet')),
            array('value' => 'custom', 'label' => Mage::helper('sociallogin')->__('Custom')),
        );
    }
}
