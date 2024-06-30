<?php declare(strict_types=1);

namespace scc\components;

/**
 * Class InputGroupPrepend
 * @package scc\components
 */
class InputGroupPrepend extends InputGroupAddon
{
    /**
     * InputGroupPrepend constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName('inputgroupprepend');

        foreach ($this->params as $param) {
            if ($param->getName() === 'append') {
                $param->setDefaultValue(false);
                $param->setValue(false);
                break;
            }
        }
    }
}
