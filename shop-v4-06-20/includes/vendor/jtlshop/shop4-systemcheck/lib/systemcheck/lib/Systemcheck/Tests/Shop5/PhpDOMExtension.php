<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Class Systemcheck_Tests_Shop5_PhpXMLExtension
 */
class Systemcheck_Tests_Shop5_PhpDOMExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'DOM-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'JTL-Shop benötigt die PHP-Erweiterung <code>php-dom</code>.';
    protected $isOptional    = false;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = extension_loaded('dom')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
