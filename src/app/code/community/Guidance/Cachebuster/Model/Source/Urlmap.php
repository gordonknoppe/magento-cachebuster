<?php
class Guidance_Cachebuster_Model_Source_Urlmap
{
    public function toOptionArray()
    {
        $options = array(
            array('value' => Mage_Core_Model_Store::URL_TYPE_JS,    'label' => '/js/'),
            array('value' => Mage_Core_Model_Store::URL_TYPE_MEDIA, 'label' => '/media/'),
            array('value' => Mage_Core_Model_Store::URL_TYPE_SKIN,  'label' => '/skin/'),
        );

        return $options;
    }
}