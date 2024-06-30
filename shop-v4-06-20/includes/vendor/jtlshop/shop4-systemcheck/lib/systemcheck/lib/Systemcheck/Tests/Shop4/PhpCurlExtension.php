<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpCurlExtension
 */
class Systemcheck_Tests_Shop4_PhpCurlExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'cURL-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = '';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = extension_loaded('curl')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}

