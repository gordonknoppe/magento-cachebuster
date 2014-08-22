<?php
/**
 * NOTICE OF LICENSE
 *
 * Copyright 2012 Guidance Solutions
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
 * @copyright  Copyright (c) 2012 Guidance Solutions (http://www.guidance.com)
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

class Guidance_Cachebuster_Model_Observer
{
    /**
     * Process response by performing a search and replace on urls to be processed
     *
     * @param Varien_Event_Observer $observer
     */
    public function controller_action_postdispatch(Varien_Event_Observer $observer)
    {
        /** @var $helper Guidance_Cachebuster_Helper_Data */
        $helper = Mage::helper('guidance_cachebuster');
        if (!$helper->isEnabled()) {
            return;
        }

        /** @var Guidance_Cachebuster_Model_Parser $parser */
        $parser   = $helper->getParser();

        /** @var Mage_Core_Controller_Response_Http $response */
        $response = $observer->getData('controller_action')->getResponse();
        $startTime = microtime(true);
        $body     = $parser->parseHtml($response->getBody());
        if ($helper->isProfilingEnabled()) {
            $endTime = microtime(true);
            $renderTime = $endTime - $startTime;
            $response->setHeader('X-Cachebuster-Time', $renderTime);
        }
        $response->setBody($body);
    }
}
