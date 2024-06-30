<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Systemcheck_Tests_Shop5_PhpImagickExtension
 */
class Systemcheck_Tests_Shop5_PhpXmlSimple extends Systemcheck_Tests_PhpModuleTest
{
    protected $name          = 'SimpleXML-Unterstützung';
    protected $requiredState = 'enabled';
    protected $description   = 'Für JTL-Shop wird die PHP-Erweiterung Simple-XML benötigt.';
    protected $isOptional    = false;
    protected $isRecommended = true;

    /**
     * Checking if the 'simplexml_load_string()'-function is usable
     *
     * @return void
     */
    public function execute()
    {
        $this->result = Systemcheck_Tests_Test::RESULT_FAILED;
        if (extension_loaded('libxml') && extension_loaded('simplexml')) {
            // simplexml is loaded, but we need to check if it's actually working
            is_a(simplexml_load_string('<?xml version="1.0"?><document></document>'), 'SimpleXMLElement')
                ? $this->result = Systemcheck_Tests_Test::RESULT_OK
                : $this->result = Systemcheck_Tests_Test::RESULT_FAILED;
        }
    }
}
