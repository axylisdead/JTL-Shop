<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpAllowUrlFopen
 */
class Systemcheck_Tests_Shop4_PhpAllowUrlFopen extends Systemcheck_Tests_PhpConfigTest
{
    protected $name          = 'allow_url_fopen';
    protected $requiredState = 'on';
    protected $description   = '';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $allow_url_fopen    = (bool)ini_get('allow_url_fopen');
        $this->currentState = $allow_url_fopen ? 'on' : 'off';
        $this->result       = $allow_url_fopen === true
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
