Magento Cachebuster
===================

Cachebuster is a Magento module which facilitates automatic purging of static assets from HTTP caches such as browser cache, CDN, Varnish, etc using best practices outlined within the HTML5 boilerplate community.

https://github.com/h5bp/html5-boilerplate/wiki/cachebusting

URLs affected:

* JS 
* Media
* Skin

## Overview

The module provides cachebusting by automatically altering the URI created by Magento for a static file by adding the timestamp of the file (filemtime()):

* Before: http://www.example.com/js/varien/js.js
* After:  http://www.example.com/js/varien/js.1324429472.js

## Installation

* Copy module files into your application
* Configure apache with the mod_rewrite rules necessary for resolving the new filenames
* Enable the module in the Magento configuration

## Configuration

This module is configured via the "System" configuration section under the group "Cachebuster Settings"

* **Enable cachebuster**
  * Enables the module behavior which rewrites URLs on the frontend.  It is important that your mod_rewrite rules are configured before enabling this setting.
* **File extensions**
  * Comma-separated list of file extensions which will be rewritten with timestamp applied.  Extensions configured here must be defined in your rewrite rule. 

## mod_rewrite configuration

The following mod_rewrite rules need to be enabled for your store when using this module, potentially via .htaccess file or Virtualhost definition.

    <IfModule mod_rewrite.c>

    ############################################
    ## rewrite files for magento cachebuster

        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.+)\.(\d+)\.(js|css|png|jpg|gif)$ $1.$3 [L]

    </IfModule>
