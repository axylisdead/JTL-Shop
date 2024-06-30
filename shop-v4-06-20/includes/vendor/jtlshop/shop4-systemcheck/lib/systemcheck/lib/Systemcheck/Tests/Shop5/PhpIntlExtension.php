<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Class Systemcheck_Tests_Shop5_PhpIntlExtension
 */
class Systemcheck_Tests_Shop5_PhpIntlExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'Internationalisierungs-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-intl</code> für die Internationalisierung.';
    protected $isOptional    = false;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = extension_loaded('intl')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
