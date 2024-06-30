<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;

/**
 * Class CardHeader
 * @package scc\components
 */
class CardHeader extends AbstractBlockComponent
{
    /**
     * CardHeader constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cardheader.tpl');
        $this->setName('cardheader');
        $this->addParam(new ComponentProperty('header'));
        $this->addParam(new ComponentProperty('header-tag', 'div'));
        $this->addParam(new ComponentProperty('header-bg-variant'));
        $this->addParam(new ComponentProperty('header-border-variant'));
        $this->addParam(new ComponentProperty('header-text-variant'));
        $this->addParam(new ComponentProperty('header-class'));
    }
}
