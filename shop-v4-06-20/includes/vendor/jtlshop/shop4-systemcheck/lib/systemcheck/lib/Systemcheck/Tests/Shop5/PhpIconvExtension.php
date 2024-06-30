<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Class Systemcheck_Tests_Shop5_PhpIconvExtension
 */
class Systemcheck_Tests_Shop5_PhpIconvExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'Iconv-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-iconv</code> für die Internationalisierung.';
    protected $isOptional    = false;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = extension_loaded('iconv')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
