<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpSocketSupport
 */
class Systemcheck_Tests_Shop4_PhpSocketSupport extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'Socket-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = '';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = function_exists('fsockopen')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
