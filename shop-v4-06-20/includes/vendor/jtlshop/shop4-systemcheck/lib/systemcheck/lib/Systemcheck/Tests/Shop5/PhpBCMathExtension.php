<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Class Systemcheck_Tests_Shop5_PhpBCMathExtension
 */
class Systemcheck_Tests_Shop5_PhpBCMathExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'BCMath-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-bcmath</code> für diverse Berechnungen.';
    protected $isOptional    = false;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = extension_loaded('bcmath')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
