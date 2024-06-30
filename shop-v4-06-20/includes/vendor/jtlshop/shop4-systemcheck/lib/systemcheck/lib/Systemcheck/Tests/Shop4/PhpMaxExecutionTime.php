<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpMaxExecutionTime
 */
class Systemcheck_Tests_Shop4_PhpMaxExecutionTime extends Systemcheck_Tests_PhpConfigTest
{
    protected $name          = 'max_execution_time';
    protected $requiredState = '>= 120';
    protected $description   = 'Für den Betrieb von JTL-Shop wird eine ausreichend lange Skriptlaufzeit benötigt, damit auch längere Aufgaben (z.B. Newsletterversand) zuverlässig funktionieren.';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $max_execution_time = ini_get('max_execution_time');
        $this->currentState = $max_execution_time;

        $this->result = Systemcheck_Tests_Test::RESULT_FAILED;
        if ($max_execution_time == 0 || $max_execution_time >= 120) {
            $this->result = Systemcheck_Tests_Test::RESULT_OK;
        }
    }
}
