<?php declare(strict_types=1);

namespace JTL\Checkbox\CheckboxFunction;

use JTL\DataObjects\AbstractDomainObject;

/**
 * Class CheckboxFunctionDomainObject
 * @package JTL\Checkbox\CheckboxFunction
 */
class CheckboxFunctionDomainObject extends AbstractDomainObject
{
    /**
     * @var string
     */
    private string $primaryKey;

    public function __construct(
        protected readonly ?int $pluginID = null,
        protected readonly ?int $checkboxFunctionID = 0,
        protected readonly string $name = '',
        protected readonly string $identifier = '',
        array $modifiedKeys = []
    ) {
        $this->primaryKey = 'checkboxFunctionID';

        parent::__construct($modifiedKeys);
    }

    /**
     * @return array
     */
    private function getMappingArray(): array
    {
        return [
            'checkboxFunctionID' => 'checkboxFunctionID',
            'pluginID'           => 'pluginID',
            'name'               => 'name',
            'identifier'         => 'identifier',
        ];
    }

    /**
     * @return array
     */
    private function getColumnMappingArray(): array
    {
        return [
            'kCheckBoxFunktion' => 'checkboxFunctionID',
            'kPlugin'           => 'pluginID',
            'cName'             => 'name',
            'cID'               => 'identifier',
            'modifiedKeys'      => 'modifiedKeys',
        ];
    }

    /**
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @return string[]
     */
    public function getMapping(): array
    {
        return \array_merge($this->getMappingArray(), $this->getColumnMappingArray());
    }

    /**
     * @return array
     */
    public function getReverseMapping(): array
    {
        return \array_flip($this->getMappingArray());
    }

    /**
     * @return array
     */
    public function getColumnMapping(): array
    {
        return \array_flip($this->getColumnMappingArray());
    }

    /**
     * @return int
     */
    public function getID(): int
    {
        return (int)$this->{$this->getPrimaryKey()};
    }

    /**
     * @return int|null
     */
    public function getPluginID(): ?int
    {
        return $this->pluginID;
    }

    /**
     * @return int
     */
    public function getCheckboxFunctionID(): int
    {
        return $this->checkboxFunctionID;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
