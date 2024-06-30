<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;
use scc\ComponentPropertyType;

/**
 * Class CardGroup
 * @package scc\components
 */
class CardGroup extends AbstractBlockComponent
{
    /**
     * CardGroup constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cardgroup.tpl');
        $this->setName('cardgroup');

        $this->addParam(new ComponentProperty('tag', 'div'));
        $this->addParam(new ComponentProperty('deck', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('columns', false, ComponentPropertyType::TYPE_BOOL));
    }
}
