<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpGdExtension
 */
class Systemcheck_Tests_Shop4_PhpGdExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name            = 'GD-Unterstützung';
    protected $requiredState   = 'enabled';
    protected $description     = '';
    protected $isOptional      = false;
    protected $isRecommended   = false;
    protected $isReplaceableBy = 'Systemcheck_Tests_Shop4_PhpImagickExtension';

    public function execute()
    {
        $this->result = extension_loaded('gd')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
