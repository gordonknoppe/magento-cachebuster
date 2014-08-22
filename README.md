Magento Cachebuster
===================

Cachebuster is a Magento module which facilitates automatic purging of static assets from HTTP caches such as browser cache, CDN, Varnish, etc using best practices outlined within the HTML5 boilerplate community.

See section "Filename-based cache busting" in https://github.com/h5bp/html5-boilerplate/blob/master/.htaccess

URLs affected:

* /js/ 
* /media/
* /skin/

## Overview

The module provides cachebusting by automatically altering the URI created by Magento for static files by adding the timestamp of the file to the filename:

* Before: http://www.example.com/js/varien/js.js
* After:  http://www.example.com/js/varien/js.1324429472.js

**Note:** Behavior only takes affect on the frontend, admin area static file urls are not processed

## Example uses

* Automatically invalidating cache when using Cloudfront CDN (see http://www.aschroder.com/2011/05/magento-and-amazons-cloudfront-cdn-the-easy-way/ for a great how-to)
  * Amazon's Cloudfront CDN can be configured to use an origin server but by it's nature will not refresh your updated file until it's cache time expires or you send an invalidation request using their API.  
* No more browser cache issues (ie. Them: "Where's that CSS change I requested?".  You: "Oh, did you hit refresh?")
  * Magento's default .htaccess file uses far-future expires headers, which is good for reducing the number of requests to your server, but also means that even without a CDN you have probably experienced browser cache causing a waste of time on what turns out to be a non-issues.

## Installation

* Copy module files into your application
* Configure apache with the mod_rewrite rules necessary for resolving the new filenames
* Enable the module in the Magento configuration

## Configuration

This module is configured via the "System" configuration section under the group "Cachebuster Settings".

* **Enable cachebuster**
  * Enables the module behavior which rewrites URLs on the frontend.  It is important that your mod_rewrite rules are configured before enabling this setting.
* **URL map**
  * Configure which types of URLs will have the signature added to the filename.
* **File extensions**
  * Comma-separated list of file extensions which will be rewritten with signature applied.  Extensions configured here must be defined in your rewrite rule. 
* **Signature**
  * Mechanism to use to determine the signature added to the URL
    * Timestamp - Signs the file using the unix timestamp last-modified time of the file
    * Sha1 numeric - Signs the file by getting the sha1 hash of the file and then extracting only the numbers from the checksum (to remain compatible with the published rewrite rules)
* **Enable profiling**
  * Adds X-Cachebuster-Time header to the response

## mod_rewrite configuration

The following mod_rewrite rules need to be enabled for your store when using this module, potentially via `.htaccess` file or Virtualhost definition.  

    <IfModule mod_rewrite.c>

    ############################################
    ## rewrite files for magento cachebuster

        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.+)\.(\d+)\.(js|css|png|jpeg|jpg|gif)$ $1.$3 [L]

    </IfModule>

If you are using the default media `.htaccess` file which routes missing URLs through Magento's `get.php` for downloadable products you will also need to add these rules to your `.htaccess` file in the `/media/` directory.

**Note:** This rewrite condition in the media directory will break the protection provided by downloadable products for the extensions listed.  If your store sells downloadable products with one of the above extensions you will likely need to tweak these conditions.

## nginx configuration

For nginx you will need to add a rule like the following to your site definition.

    location ~* (.+)\.(\d+)\.(js|css|png|jpg|jpeg|gif)$ {
        try_files $uri $1.$3;
    }

## License

Licensed under the Apache License, Version 2.0
