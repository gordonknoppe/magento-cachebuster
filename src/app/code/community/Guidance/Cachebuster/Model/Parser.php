<?php
/**
 * @author      Guidance Magento Team <magento@guidance.com>
 * @category    Guidance
 * @package     Cachebuster
 * @copyright   Copyright (c) 2013 Guidance Solutions (http://www.guidance.com)
 */

class Guidance_Cachebuster_Model_Parser
{
    /** @var array  */
    protected $_maps = array();

    /** @var array  */
    protected $_fileExtensions = array();

    /** @var  callable */
    protected $_callback;

    public function __construct($config = array())
    {
        if (isset($config['urlMap'])) {
            $this->setMaps($config['urlMap']);
        }
        if (isset($config['fileExtensions'])) {
            $this->setFileExtensions($config['fileExtensions']);
        }
        if (isset($config['callback'])) {
            $this->setCallback($config['callback']);
        }
    }

    /**
     * Parse given html
     *
     * @param string $html
     */
    public function parseHtml($html = '')
    {
        $find = $processed = array();

        // Get urls to add signature to by map type
        $urls = $this->_parseUrls($html);

        foreach ($urls as $urlKey => $foundUrls) {

            // File system path for urlKey
            $path = $this->getPathByUrl($urlKey);

            foreach ($foundUrls as $url) {
                // Skip this url if it has already been processed
                if (isset($processed[$url])) {
                    continue;
                }

                $signedUrl = $this->_addSignatureToUrl($url, $urlKey, $path);

                if ($url != $signedUrl) {
                    $find[$url] = $signedUrl;
                    $processed[$url] = true;
                }
            }
        }

        // Search and replace html
        if (count($find)) {
            $html = str_replace(array_keys($find), array_values($find), $html);
        }

        // Return html
        return $html;
    }

    /**
     * Add signature to the filename portion of URL using $basePath to determine signature for URL
     *
     * @param $url
     * @param $baseUrl
     * @param $basePath
     * @return mixed|string
     */
    protected function _addSignatureToUrl($url, $baseUrl, $basePath)
    {
        $url = $this->_sanitizeUrl($url);
        $baseUrl = $this->_sanitizeUrl($baseUrl);

        $path = str_replace($baseUrl, $basePath, $url);
        $pathinfo = pathinfo($path);

        if (empty($pathinfo['extension']) || empty($pathinfo['filename']) || empty($pathinfo['basename'])
            || !in_array($pathinfo['extension'], $this->getFileExtensions())
            || !file_exists($path)
        ) {
            return $url;
        }

        if (is_callable($this->getCallback())) {
            $signature = call_user_func($this->getCallback(), $path);
        } else {
            $signature = filemtime($path);
        }

        $final = array(
            $pathinfo['filename'],
            $signature,
            $pathinfo['extension'],
        );

        return str_replace($pathinfo['basename'], implode('.', $final), $url);
    }

    /**
     * Sanitize URL by removing query, fragment, user, or pass if found
     *
     * @param $url
     * @return string
     */
    protected function _sanitizeUrl($url)
    {
        $url    = parse_url($url);
        $scheme = isset($url['scheme']) ? $url['scheme'] . '://' : '';
        $host   = isset($url['host']) ? $url['host'] : '';
        $port   = isset($url['port']) ? ':' . $url['port'] : '';
        $path   = isset($url['path']) ? $url['path'] : '';
        return "$scheme$host$port$path";
    }

    /**
     * @param string $html
     * @return array
     */
    protected function _parseUrls($html)
    {
        // Urls extracted from html by map type
        $urls = array();

        foreach (array_keys($this->getMaps()) as $url) {
            $matches = array();
            $regex = "|${url}[^\"\'\s]*|";

            preg_match_all($regex, $html, $matches);

            if (isset($matches[0]) && is_array($matches[0]) && count($matches[0])) {
                $urls[$url] = $matches[0];
            }
        }
        return $urls;
    }

    /**
     * @param $url
     * @param $path
     * @return $this
     */
    public function addMap($url, $path)
    {
        $this->validate($url, $path);
        $this->_maps[$url] = $path;
        return $this;
    }

    /**
     * @return array
     */
    public function getMaps()
    {
        return $this->_maps;
    }

    /**
     * @param string $url
     * @return string
     */
    public function getPathByUrl($url)
    {
        return $this->_maps[$url];
    }

    /**
     * @param array $maps
     * @return $this
     */
    public function setMaps($maps = array())
    {
        if (!is_array($maps) || empty($maps)) {
            throw new InvalidArgumentException('Maps must be an array of type "url" => "path"');
        }
        foreach ($maps as $url => $path) {
            $this->validate($url, $path);
        }
        $this->_maps = $maps;
        return $this;
    }

    /**
     * @param array $extensions
     * @return $this
     */
    public function setFileExtensions($extensions = array())
    {
        if (!is_array($extensions) || empty($extensions)) {
            throw new InvalidArgumentException('Extensions must be a non empty array');
        }
        $this->_fileExtensions = $extensions;
        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->_callback = $callback;
        return $this;
    }

    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * @return array
     */
    public function getFileExtensions()
    {
        return $this->_fileExtensions;
    }

    /**
     * @param string $url
     * @param string $path
     * @return $this
     * @throws InvalidArgumentException
     */
    protected function validate($url, $path)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('url must be a valid url, given: ' . $url);
        }
        if (!file_exists($path)) {
            throw new InvalidArgumentException('path must be a valid path which exists: ' . $path . ' not found');
        }
        return $this;
    }
}