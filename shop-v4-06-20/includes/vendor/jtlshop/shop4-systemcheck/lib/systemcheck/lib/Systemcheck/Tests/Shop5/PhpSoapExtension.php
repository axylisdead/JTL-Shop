<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Systemcheck_Tests_Shop5_PhpSoapExtension
 */
class Systemcheck_Tests_Shop5_PhpSoapExtension extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'SOAP-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'Die Prüfung der Umsatzsteuer-ID erfolgt per "MwSt-Informationsaustauschsystem (MIAS) '.
        'der Europäischen Kommission".<br> Dieses System wird mit dem Übertragungsprotokoll "SOAP" abgefragt, was '.
        'eine entsprechende PHP-Unterstützung voraussetzt.';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $this->result = class_exists('SoapClient')
            ? Systemcheck_Tests_Test::RESULT_OK
            : Systemcheck_Tests_Test::RESULT_FAILED;
    }
}
