<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;

/**
 * Class FormRow
 * @package scc\components
 */
class FormRow extends AbstractBlockComponent
{
    /**
     * FormRow constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('formrow.tpl');
        $this->setName('formrow');
        $this->addParam(new ComponentProperty('tag', 'div'));
    }
}
