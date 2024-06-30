<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;

/**
 * Class InputGroupText
 * @package scc\components
 */
class InputGroupText extends AbstractBlockComponent
{
    /**
     * InputGroupText constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('inputgrouptext.tpl');
        $this->setName('inputgrouptext');
        $this->addParam(new ComponentProperty('tag', 'div'));
    }
}
