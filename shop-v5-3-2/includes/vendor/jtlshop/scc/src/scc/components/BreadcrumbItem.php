<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentProperty;
use scc\ComponentPropertyType;

/**
 * Class BreadcrumbItem
 * @package scc\components
 */
class BreadcrumbItem extends AbstractBlockComponent
{
    /**
     * BreadcrumbItem constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('breadcrumbitem.tpl');
        $this->setName('breadcrumbitem');

        $this->addParam(new ComponentProperty('href'));
        $this->addParam(new ComponentProperty('tag', 'li'));
        $this->addParam(new ComponentProperty('target', '_self'));
        $this->addParam(new ComponentProperty('active-class', 'active'));
        $this->addParam(new ComponentProperty('router-tag', 'a'));
        $this->addParam(new ComponentProperty('router-tag-itemprop'));
        $this->addParam(new ComponentProperty('exact-active-class', 'active'));
        $this->addParam(new ComponentProperty('active', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('disabled', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('append', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('exact', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('replace', false, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('nofollow', false, ComponentPropertyType::TYPE_BOOL));
    }
}
