<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpImagickExtension
 */
class Systemcheck_Tests_Shop4_PhpZipArchive extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'ziparchive';
    protected $requiredState = 'enabled';
    protected $description   = 'Zum Erstellen von diversen Exporten wird die Installation der PHP-Klasse "ZipArchive" empfohlen.';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = class_exists('ZipArchive')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
