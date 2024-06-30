<?php declare(strict_types=1);

namespace scc;

/**
 * Class ComponentProperty
 * @package scc
 */
class ComponentProperty implements ComponentPropertyInterface
{
    /**
     * @var bool
     */
    private bool $isRequired = false;

    /**
     * @var mixed
     */
    private mixed $value = null;

    /**
     * ComponentProperty constructor.
     * @param string $name
     * @param mixed  $defaultValue
     * @param string $type
     */
    public function __construct(
        private string $name,
        private mixed $defaultValue = null,
        private string $type = ComponentPropertyType::TYPE_STRING)
    {
    }

    /**
     * @inheritdoc
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultValue($value): void
    {
        $this->defaultValue = $value;
    }

    /**
     * @return bool
     */
    public function getIsRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * @inheritdoc
     */
    public function setIsRequired(bool $required): void
    {
        $this->isRequired = $required;
    }

    /**
     * @return string
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

    /**
     * @return string
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function hasValue(): bool
    {
        return $this->value !== null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)($this->value ?? '');
    }
}
