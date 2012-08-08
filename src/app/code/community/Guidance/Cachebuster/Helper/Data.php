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
    const XML_PATH_FILE_EXTENSIONS = 'system/guidance_cachebuster/file_extensions';

    protected $_fileExtensions;

    public function isEnabled()
    {
        return Mage::getStoreConfig(self::XML_PATH_IS_ENABLED);
    }

    public function enabledFileExtensions()
    {
        if (is_null($this->_fileExtensions)) {
            $config = Mage::getStoreConfig(self::XML_PATH_FILE_EXTENSIONS);
            $this->_fileExtensions = array_map('trim', explode(',', $config));
        }
        return $this->_fileExtensions;
    }

}