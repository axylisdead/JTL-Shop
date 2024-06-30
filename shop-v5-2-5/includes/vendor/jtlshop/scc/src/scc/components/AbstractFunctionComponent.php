<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentType;
use scc\renderers\FunctionRenderer;

/**
 * Class AbstractFunctionComponent
 * @package scc\components
 */
abstract class AbstractFunctionComponent extends AbstractBaseComponent
{
    /**
     * AbstractFunctionComponent constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setRenderer(new FunctionRenderer($this));
        $this->setType(ComponentType::TYPE_FUNCTION);
    }
}
