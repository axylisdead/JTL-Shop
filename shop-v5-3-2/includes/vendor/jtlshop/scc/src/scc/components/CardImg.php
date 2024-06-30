<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;
use scc\ComponentPropertyType;

/**
 * Class CardImg
 * @package scc\components
 */
class CardImg extends AbstractFunctionComponent
{
    /**
     * CardImg constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cardimg.tpl');
        $this->setName('cardimg');

        $this->addParam(new ComponentProperty('src'));
        $this->addParam(new ComponentProperty('alt', ''));
        $this->addParam(new ComponentProperty('top', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('bottom', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('fluid', false, ComponentPropertyType::TYPE_BOOL));
    }
}
