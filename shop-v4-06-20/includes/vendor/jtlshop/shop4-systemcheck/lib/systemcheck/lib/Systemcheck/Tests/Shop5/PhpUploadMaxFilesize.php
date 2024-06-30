<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop5
 */

/**
 * Systemcheck_Tests_Shop5_PhpUploadMaxFilesize
 */
class Systemcheck_Tests_Shop5_PhpUploadMaxFilesize extends Systemcheck_Tests_PhpConfigTest
{
    protected $name          = 'upload_max_filesize';
    protected $requiredState = '>= 6M';
    protected $description   = '';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $upload_max_filesize = ini_get('upload_max_filesize');
        $this->currentState  = $upload_max_filesize;

        $this->result = Systemcheck_Tests_Test::RESULT_FAILED;
        if ($this->shortHandToInt($upload_max_filesize) >= $this->shortHandToInt('6M')) {
            $this->result = Systemcheck_Tests_Test::RESULT_OK;
        }
    }
}
