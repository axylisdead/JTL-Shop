<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpJsonExtension
 */
class Systemcheck_Tests_Shop4_PhpJsonExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'JSON-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'JTL-Shop benötigt PHP-Unterstützung für das JSON-Format.<br>In neueren Debian-PHP-Paketen wird die Unterstützung für JSON standardmäßig nicht mehr mitinstalliert. Hierfür ist die Installation des Pakets <code>php5-json</code> erforderlich.';
    protected $isOptional    = false;
    protected $isRecommended = false;

    public function execute()
    {
        $this->result = Systemcheck_Tests_Test::RESULT_FAILED;

        if (function_exists('json_encode') && function_exists('json_decode')) {
            $this->result = Systemcheck_Tests_Test::RESULT_OK;
        }
    }
}
