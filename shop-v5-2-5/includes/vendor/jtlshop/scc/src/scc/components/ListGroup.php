<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;

/**
 * Class ListGroup
 * @package scc\components
 */
class ListGroup extends AbstractBlockComponent
{
    /**
     * ListGroup constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('listgroup.tpl');
        $this->setName('listgroup');
        $this->addParam(new ComponentProperty('tag', 'div'));
    }
}
