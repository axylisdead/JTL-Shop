<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Systemcheck_Tests_Shop5_PhpVersion
 */
class Systemcheck_Tests_Shop5_PhpVersion extends Systemcheck_Tests_ProgramTest
{
    protected $name          = 'PHP-Version';
    protected $requiredState = '>= 7.2.0';
    protected $description   = '';
    protected $isOptional    = false;
    protected $isRecommended = false;

    public function execute()
    {
        $version            = phpversion();
        $this->currentState = $version;
        $this->result       = Systemcheck_Tests_Test::RESULT_FAILED;

        if (version_compare($version, '7.2.0', '>=')) {
            $this->result = Systemcheck_Tests_Test::RESULT_OK;
        }
    }
}
