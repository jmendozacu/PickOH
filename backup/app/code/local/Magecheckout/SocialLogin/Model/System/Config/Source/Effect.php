<?php

class Magecheckout_SocialLogin_Model_System_Config_Source_Effect
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'mfp-zoom-in', 'label' =>Mage::helper('sociallogin')->__('Zoom')),
            array('value' => 'mfp-newspaper', 'label' =>Mage::helper('sociallogin')->__('Newspaper')),
            array('value' => 'mfp-move-horizontal', 'label' =>Mage::helper('sociallogin')->__('Horizontal move')),
            array('value' => 'mfp-move-from-top', 'label' =>Mage::helper('sociallogin')->__('Move from top')),
            array('value' => 'mfp-3d-unfold', 'label' =>Mage::helper('sociallogin')->__('3D unfold')),
            array('value' => 'mfp-zoom-out', 'label' =>Mage::helper('sociallogin')->__('Zoom-out'))
        );
    }
}
