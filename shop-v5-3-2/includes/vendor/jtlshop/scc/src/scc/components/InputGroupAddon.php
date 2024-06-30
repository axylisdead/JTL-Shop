<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;
use scc\ComponentPropertyType;

/**
 * Class InputGroupAddon
 * @package scc\components
 */
class InputGroupAddon extends AbstractBlockComponent
{
    /**
     * InputGroupAddon constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('inputgroupaddon.tpl');
        $this->setName('inputgroupaddon');

        $this->addParam(new ComponentProperty('append', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('is-text', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('tag', 'div'));
    }
}
