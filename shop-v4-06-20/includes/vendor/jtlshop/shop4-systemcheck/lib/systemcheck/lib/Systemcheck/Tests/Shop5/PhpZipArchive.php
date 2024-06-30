<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Systemcheck_Tests_Shop5_PhpImagickExtension
 */
class Systemcheck_Tests_Shop5_PhpZipArchive extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'ziparchive';
    protected $requiredState = 'enabled';
    protected $description   = 'Zum Erstellen von diversen Exporten wird die Installation der PHP-Klasse "ZipArchive" benötigt.';
    protected $isOptional    = false;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = class_exists('ZipArchive')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
