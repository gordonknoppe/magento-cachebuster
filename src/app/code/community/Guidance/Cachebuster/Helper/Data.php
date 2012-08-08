<?php
/**
 * @author      Gordon Knoppe
 * @category    Guidance
 * @package     Cachebuster
 * @copyright   Copyright (c) 2012 Guidance Solutions (http://www.guidance.com)
 * @license
 */

class Guidance_Cachebuster_Helper_Data extends Mage_Core_Helper_Data
{

    const XML_PATH_IS_ENABLED = 'system/guidance_cachebuster/is_enabled';

    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_IS_ENABLED);
    }

}