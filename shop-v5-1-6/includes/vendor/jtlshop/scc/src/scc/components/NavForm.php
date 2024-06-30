<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;

/**
 * Class NavForm
 * @package scc\components
 */
class NavForm extends AbstractBlockComponent
{
    /**
     * NavForm constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('navform.tpl');
        $this->setName('navform');
        $this->addParam(new ComponentProperty('action', ''));
        $this->addParam(new ComponentProperty('method', 'POST'));
    }
}
