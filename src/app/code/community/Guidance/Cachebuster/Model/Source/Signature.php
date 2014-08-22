<?php
/**
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     Cachebuster
 * @copyright   Copyright (c) 2013 Guidance Solutions (http://www.guidance.com)
 */

class Guidance_Cachebuster_Model_Source_Signature
{
    public function toOptionArray()
    {
        $options = array(
            array('value' => Guidance_Cachebuster_Helper_Data::SIGNATURE_TIMESTAMP,    'label' => 'File timestamp'),
            array('value' => Guidance_Cachebuster_Helper_Data::SIGNATURE_SHA1_NUMERIC, 'label' => 'Sha1 (numbers only)'),
        );
        return $options;
    }

}