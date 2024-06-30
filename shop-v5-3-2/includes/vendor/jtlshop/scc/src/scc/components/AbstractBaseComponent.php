<?php declare(strict_types=1);

namespace scc\components;

use scc\ComponentInterface;
use scc\ComponentProperty;
use scc\ComponentPropertyInterface;
use scc\ComponentPropertyType;
use scc\ComponentRendererInterface;

/**
 * Class AbstractBaseComponent
 * @package scc\components
 */
abstract class AbstractBaseComponent implements ComponentInterface
{
    /**
     * @var string|null
     */
    protected ?string $content = null;

    /**
     * @var ComponentPropertyInterface[]
     */
    protected array $params;

    /**
     * @var string
     */
    protected string $template;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var ComponentRendererInterface
     */
    protected ComponentRendererInterface $renderer;

    /**
     * @var string
     */
    protected string $type;

    /**
     * AbstractBaseComponent constructor.
     */
    public function __construct()
    {
        $this->addParam(new ComponentProperty('class'));
        $this->addParam(new ComponentProperty('title'));
        $this->addParam(new ComponentProperty('id'));
        $this->addParam(new ComponentProperty('style'));
        $this->addParam(new ComponentProperty('itemprop'));
        $this->addParam(new ComponentProperty('itemtype'));
        $this->addParam(new ComponentProperty('itemid'));
        $this->addParam(new ComponentProperty('role'));
        $this->addParam(new ComponentProperty('itemscope', null, ComponentPropertyType::TYPE_BOOL));
        $this->addParam(new ComponentProperty('aria', null, ComponentPropertyType::TYPE_ARRAY));
        $this->addParam(new ComponentProperty('data', null, ComponentPropertyType::TYPE_ARRAY));
        $this->addParam(new ComponentProperty('attribs', null, ComponentPropertyType::TYPE_ARRAY));
    }

    /**
     * @inheritdoc
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @inheritdoc
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @inheritdoc
     */
    public function addParam(ComponentProperty $param): void
    {
        $this->params[$param->getName()] = $param;
    }

    /**
     * @inheritdoc
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @inheritdoc
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getRenderer(): ComponentRendererInterface
    {
        return $this->renderer;
    }

    /**
     * @inheritdoc
     */
    public function setRenderer(ComponentRendererInterface $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
