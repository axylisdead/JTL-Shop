<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Test type for checking whether a certain PHP config option has been configured correctly
 */
abstract class Systemcheck_Tests_PhpConfigTest extends Systemcheck_Tests_Test 
{
    protected function shortHandToInt($shorthand)
    {
        switch (substr ($shorthand, -1))
        {
            case 'M':
            case 'm':
                return (int)$shorthand * 1048576;
            
            case 'K':
            case 'k':
                return (int)$shorthand * 1024;
            
            case 'G':
            case 'g':
                return (int)$shorthand * 1073741824;
            
            default:
                return $shorthand;
        }
    }
}
