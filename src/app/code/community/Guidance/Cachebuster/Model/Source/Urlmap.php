<?php
/**
 * NOTICE OF LICENSE
 *
 * Copyright 2015 Gordon Knoppe
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author     Gordon Knoppe
 * @category   Guidance
 * @package    Cachebuster
 * @copyright  Copyright (c) 2015 Gordon Knoppe
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

class Guidance_Cachebuster_Model_Source_Urlmap
{
    /**
     * Source map option array
     *
     * @return array
     */
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