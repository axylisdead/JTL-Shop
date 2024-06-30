<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpMagicQuotesGpc
 */
class Systemcheck_Tests_Shop4_PhpMagicQuotesGpc extends Systemcheck_Tests_PhpConfigTest
{
    protected $name          = 'magic_quotes_gpc';
    protected $requiredState = 'off';
    protected $description   = '';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $magic_quotes_runtime = (bool)ini_get('magic_quotes_gpc');
        $this->currentState   = $magic_quotes_runtime ? 'on' : 'off';

        $this->result = Systemcheck_Tests_Test::RESULT_FAILED;
        if ($magic_quotes_runtime === false) {
            $this->result = Systemcheck_Tests_Test::RESULT_OK;
        }
    }
}
