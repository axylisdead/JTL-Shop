<?php declare(strict_types=1);

namespace scc\components;

/**
 * Class InputGroupAppend
 * @package scc\components
 */
class InputGroupAppend extends InputGroupAddon
{
    /**
     * InputGroupAppend constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName('inputgroupappend');

        foreach ($this->params as $param) {
            if ($param->getName() === 'append') {
                $param->setDefaultValue(true);
                $param->setValue(true);
                break;
            }
        }
    }
}
