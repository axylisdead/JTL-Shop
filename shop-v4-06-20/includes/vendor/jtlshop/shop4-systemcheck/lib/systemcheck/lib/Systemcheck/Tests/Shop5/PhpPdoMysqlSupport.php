<?php
/**
 * @package jtl\Systemcheck\Shop5
 * @author Clemens Rudolph <clemens.rudolph@jtl-software.com>
 * @copyright 2016 JTL-Software-GmbH
 */

/**
 * Systemcheck_Tests_Shop5_PhpPdoMysqlSupport
 */
class Systemcheck_Tests_Shop5_PhpPdoMysqlSupport extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'PDO::MySQL - Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'Für JTL-Shop wird die Unterstützung für PHP-Data-Objects (<code>php-pdo</code> und <code>php-mysql</code>) benötigt.';
    protected $isOptional    = false;
    protected $isRecommended = false;

    /**
     * Execute the test for PDO and its drivers we need
     *
     * @return void
     */
    public function execute()
    {
        $this->result = (extension_loaded('pdo') && extension_loaded('pdo_mysql'))
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}

