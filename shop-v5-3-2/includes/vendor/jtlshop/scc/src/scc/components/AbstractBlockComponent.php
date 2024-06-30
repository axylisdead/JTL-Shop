<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentType;
use scc\renderers\BlockRenderer;

/**
 * Class AbstractBlockComponent
 * @package scc\components
 */
abstract class AbstractBlockComponent extends AbstractBaseComponent
{
    /**
     * AbstractBlockComponent constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType(ComponentType::TYPE_BLOCK);
        $this->setRenderer(new BlockRenderer($this));
    }
}
