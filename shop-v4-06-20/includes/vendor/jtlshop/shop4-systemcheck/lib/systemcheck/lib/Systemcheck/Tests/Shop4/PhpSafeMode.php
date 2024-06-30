<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpSafeMode
 */
class Systemcheck_Tests_Shop4_PhpSafeMode extends Systemcheck_Tests_PhpConfigTest
{
    protected $name          = 'safe_mode';
    protected $requiredState = 'off';
    protected $description   = '';
    protected $isOptional    = false;
    protected $isRecommended = false;

    public function execute()
    {
        $safe_mode          = (bool)ini_get('safe_mode');
        $this->currentState = $safe_mode ? 'on' : 'off';
        $this->result       = $safe_mode === false
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
