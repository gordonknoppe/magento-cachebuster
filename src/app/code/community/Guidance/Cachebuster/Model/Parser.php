<?php
/**
 * @author      Gordon Knoppe <gordon@knoppe.net>
 * @category    Guidance
 * @package     Cachebuster
 * @copyright   Copyright (c) 2015 Gordon Knoppe
 */

class Guidance_Cachebuster_Model_Parser
{
    /**
     * URL maps cache
     *
     * @var array  */
    protected $_maps = array();

    /**
     * File extensions cache
     *
     * @var array
     */
    protected $_fileExtensions = array();

    /**
     * Guidance_Cachebuster_Model_Parser constructor.
     * 
     * @param array $config
     */
    public function __construct($config = array())
    {
        if (isset($config['urlMap'])) {
            $this->setMaps($config['urlMap']);
        }
        if (isset($config['fileExtensions'])) {
            $this->setFileExtensions($config['fileExtensions']);
        }
    }

    /**
     * Parse given html
     *
     * @param string $html
     * @return mixed|string
     */
    public function parseHtml($html = '')
    {
        $find = $processed = array();

        // Get urls to add timestamps to by map type
        $urls = $this->_parseUrls($html);

        foreach ($urls as $urlKey => $foundUrls) {

            // File system path for urlKey
            $path = $this->getPathByUrl($urlKey);

            foreach ($foundUrls as $url) {
                // Skip this url if it has already been processed
                if (isset($processed[$url])) {
                    continue;
                }

                $timeStampedUrl = $this->_addTimestampToUrl($url, $urlKey, $path);

                if ($url != $timeStampedUrl) {
                    $find[$url] = $timeStampedUrl;
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
     * Add timestamp to the filename portion of URL using $basePath to determine timestamp for URL
     *
     * @param $url
     * @param $baseUrl
     * @param $basePath
     * @return mixed|string
     */
    protected function _addTimestampToUrl($url, $baseUrl, $basePath)
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

        $timestamp = filemtime($path);

        $final = array(
            $pathinfo['filename'],
            $timestamp,
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
     * Parse URLs in given string
     *
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
     * Add URL and path mapping
     *
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
     * Get configured URL and path mappings
     *
     * @return array
     */
    public function getMaps()
    {
        return $this->_maps;
    }

    /**
     * Get file path from URL based on mapping
     *
     * @param string $url
     * @return string
     */
    public function getPathByUrl($url)
    {
        return $this->_maps[$url];
    }

    /**
     * Set URL maps
     *
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
     * Set file extensions for parsing
     *
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
     * Get file extensions configured for parsing
     *
     * @return array
     */
    public function getFileExtensions()
    {
        return $this->_fileExtensions;
    }

    /**
     * Validate given URL and path for processing
     *
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