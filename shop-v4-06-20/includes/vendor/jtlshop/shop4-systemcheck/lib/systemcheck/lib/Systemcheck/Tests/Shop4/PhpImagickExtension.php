<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpImagickExtension
 */
class Systemcheck_Tests_Shop4_PhpImagickExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'ImageMagick-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-imagick</code> für die dynamische Generierung von Bildern.<br>Diese Erweiterung ist auf Debian-Systemen als <code>php5-imagick,</code> sowie auf Fedora/RedHat-Systemen als <code>php-pecl-imagick</code> verfügbar.';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = extension_loaded('imagick')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
