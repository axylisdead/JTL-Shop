<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Systemcheck_Tests_Shop5_PhpGdExtension
 */
class Systemcheck_Tests_Shop5_PhpGdExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name            = 'GD-Unterstützung';
    protected $requiredState   = 'enabled';
    protected $description     = '';
    protected $isOptional      = false;
    protected $isRecommended   = false;
    protected $isReplaceableBy = 'Systemcheck_Tests_Shop5_PhpImagickExtension';

    public function execute()
    {
        $this->result = extension_loaded('gd')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
