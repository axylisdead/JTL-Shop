<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpSapi
 */
class Systemcheck_Tests_Shop4_PhpSapi extends Systemcheck_Tests_ProgramTest
{
    protected $name          = 'PHP-SAPI';
    protected $requiredState = 'Apache2, FastCGI, FPM';
    protected $description   = '';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $sapi               = PHP_SAPI;
        $sapi_names         = array(
            'apache'         => 'Apache',
            'apache2filter'  => 'Apache 2.0',
            'apache2handler' => 'Apache 2.0',
            'cgi'            => 'CGI',
            'cgi-fcgi'       => 'FastCGI',
            'fpm-fcgi'       => 'FPM',
            'fpm'            => 'FPM',
            'cli'            => 'CLI'
        );
        $this->currentState = isset($sapi_names[$sapi]) ? $sapi_names[$sapi] : null;
        $this->result       = Systemcheck_Tests_Test::RESULT_FAILED;
        if (in_array($sapi, array_keys($sapi_names), true)) {
            $this->result = Systemcheck_Tests_Test::RESULT_OK;
        }
        // Refine detection in case the SAPI check gives unexpected results
        if (function_exists('fastcgi_finish_request')) {
            $sapi               = 'fpm';
            $this->currentState = $sapi_names[$sapi];
            $this->result       = Systemcheck_Tests_Test::RESULT_OK;
        }
    }
}
