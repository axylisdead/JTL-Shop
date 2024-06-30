<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_PhpPostMaxSize
 */
class Systemcheck_Tests_Shop4_PhpPostMaxSize extends Systemcheck_Tests_PhpConfigTest
{
    protected $name          = 'post_max_size';
    protected $requiredState = '>= 8M';
    protected $description   = '';
    protected $isOptional    = true;
    protected $isRecommended = true;

    public function execute()
    {
        $post_max_size      = ini_get('post_max_size');
        $this->currentState = $post_max_size;

        $this->result = Systemcheck_Tests_Test::RESULT_FAILED;
        if ($this->shortHandToInt($post_max_size) >= $this->shortHandToInt('8M')) {
            $this->result = Systemcheck_Tests_Test::RESULT_OK;
        }
    }
}
