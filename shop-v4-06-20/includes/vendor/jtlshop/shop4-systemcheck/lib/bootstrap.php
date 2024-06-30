<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */
 
require_once(dirname(__FILE__) . '/systemcheck/lib/Systemcheck/Autoloader.php');

if (version_compare(phpversion(), '5', '<')) {
	die('Sie benutzen noch PHP 4. Bitte aktualisieren Sie auf mindestens PHP 5.4, um den Systemcheck verwenden zu können.');
}

Systemcheck_Autoloader::register();
