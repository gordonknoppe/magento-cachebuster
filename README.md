Magento Cachebuster
===================

Cachebuster is a Magento module which facilitates automatic purging of static assets from HTTP caches such as browser cache, CDN, Varnish, etc using best practices outlined within the HTML5 boilerplate community.

See https://github.com/h5bp/html5-boilerplate/wiki/cachebusting

URLs affected:

* /js/ 
* /media/
* /skin/

## Overview

The module provides cachebusting by automatically altering the URI created by Magento for static files by adding the timestamp of the file to the filename:

* Before: http://www.example.com/js/varien/js.js
* After:  http://www.example.com/js/varien/js.1324429472.js

## Example uses

* Automatically invalidating cache when using Cloudfront CDN (see http://www.aschroder.com/2011/05/magento-and-amazons-cloudfront-cdn-the-easy-way/ for a great how-to)
  * Amazon's Cloudfront CDN can be configured to use an origin server but by it's nature will not refresh your updated file until it's cache time expires or you send an invalidation request using their API.  
* No more browser cache issues (ie. Them: "Where's that CSS change I requested?".  You: "Oh, did you hit refresh?")
  * Magento's default .htaccess file uses far-future expires headers, which is good for reducing the number of requests to your server, but also means that even without a CDN you have probably experienced browser cache cauing a waste of time on what turns out to be a non-issues.

## Installation

* Copy module files into your application
* Configure apache with the mod_rewrite rules necessary for resolving the new filenames
* Enable the module in the Magento configuration

## Configuration

**Attention** These settings should be set on the website scope to prevent issues with some functionality in the admin panel not working.

This module is configured via the "System" configuration section under the group "Cachebuster Settings". 

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

## License

Licensed under the Apache License, Version 2.0