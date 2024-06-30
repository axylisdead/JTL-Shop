<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Systemcheck_Tests_Shop5_PhpMemoryLimit
 */
class Systemcheck_Tests_Shop5_PhpMemoryLimit extends Systemcheck_Tests_PhpConfigTest
{
    protected $name          = 'memory_limit';
    protected $requiredState = '>= 128MB';
    protected $description   = '';
    protected $isOptional    = false;
    protected $isRecommended = false;

    public function execute()
    {
        $memory_limit       = ini_get('memory_limit');
        $this->currentState = $memory_limit;

        $this->result = Systemcheck_Tests_Test::RESULT_FAILED;
        if ($memory_limit == -1 || $this->shortHandToInt($memory_limit) >= $this->shortHandToInt('64M')) {
            $this->result = Systemcheck_Tests_Test::RESULT_OK;
        }
    }
}
