<?php declare(strict_types=1);

namespace JTL\Checkbox;

use JTL\DataObjects\AbstractDomainObject;
use JTL\DataObjects\DataTableObjectInterface;

/**
 * Class CheckboxDomainObject
 * @package JTL\Checkbox
 */
class CheckboxDomainObject extends AbstractDomainObject implements DataTableObjectInterface
{
    /**
     * @var string
     */
    private string $primaryKey;

    public function __construct(
        protected readonly int    $checkboxID = 0,
        protected readonly int    $linkID = 0,
        protected readonly int    $checkboxFunctionID = 0,
        protected readonly string $name = '',
        protected readonly string $customerGroupsSelected = '',
        protected readonly string $displayAt = '',
        protected readonly bool   $active = true,
        protected readonly bool   $isMandatory = false,
        protected readonly bool   $hasLogging = true,
        protected readonly int    $sort = 0,
        protected readonly string $created = '',
        protected readonly bool   $internal = false,
        private readonly string   $created_DE = '',
        private readonly array    $languages = [],
        private readonly bool     $nLink = false,
        private readonly array    $checkBoxLanguage_arr = [],
        private readonly array    $customerGroup_arr = [],
        private readonly array    $displayAt_arr = [],
        array $modifiedKeys = []
    ) {
        $this->primaryKey = 'checkboxID';

        parent::__construct($modifiedKeys);
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
    private function getMappingArray(): array
    {
        return [
            'checkboxID'             => 'checkboxID',
            'linkID'                 => 'linkID',
            'checkboxFunctionID'     => 'checkboxFunctionID',
            'name'                   => 'name',
            'customerGroupsSelected' => 'customerGroupsSelected',
            'displayAt'              => 'displayAt',
            'active'                 => 'active',
            'isMandatory'            => 'isMandatory',
            'hasLogging'             => 'hasLogging',
            'sort'                   => 'sort',
            'created'                => 'created',
            'nlink'                  => 'hasLink',
            'nFunction'              => 'hasFunction',
            'created_DE'             => 'createdDE',
            'oCheckBoxLanguage_arr'  => 'checkBoxLanguage_arr',
            'customerGroup_arr'      => 'customerGroup_arr',
            'displayAt_arr'          => 'displayAt_arr',
            'internal'               => 'internal',
        ];
    }

    /**
     * @return string[]
     */
    private function getColumnMappingArray(): array
    {
        return [
            'kCheckBox'            => 'checkboxID',
            'kLink'                => 'linkID',
            'kCheckBoxFunktion'    => 'checkboxFunctionID',
            'cName'                => 'name',
            'cKundengruppe'        => 'customerGroupsSelected',
            'cAnzeigeOrt'          => 'displayAt',
            'nAktiv'               => 'active',
            'nPflicht'             => 'isMandatory',
            'nLogging'             => 'hasLogging',
            'nSort'                => 'sort',
            'dErstellt'            => 'created',
            'dErstellt_DE'         => 'createdDE',
            'oCheckBoxSprache_arr' => 'checkBoxLanguage_arr',
            'kKundengruppe_arr'    => 'customerGroup_arr',
            'kAnzeigeOrt_arr'      => 'displayAt_arr',
            'nInternal'            => 'internal',
            'modifiedKeys'         => 'modifiedKeys',
        ];
    }

    /**
     * @return array
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
        return $this->{$this->getPrimaryKey()};
    }

    /**
     * @return int
     */
    public function getCheckboxID(): int
    {
        return $this->checkboxID;
    }

    /**
     * @return int
     */
    public function getLinkID(): int
    {
        return $this->linkID;
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
    public function getCustomerGroupsSelected(): string
    {
        return $this->customerGroupsSelected;
    }

    /**
     * @return string
     */
    public function getDisplayAt(): string
    {
        return $this->displayAt;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isMandatory(): bool
    {
        return $this->isMandatory;
    }

    /**
     * @return bool
     */
    public function isLogging(): bool
    {
        return $this->hasLogging;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * @return bool
     */
    public function getInternal(): bool
    {
        return $this->internal;
    }

    /**
     * @return string
     */
    public function getCreatedDE(): string
    {
        return $this->created_DE;
    }

    /**
     * @return array
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @return array
     */
    public function getCheckBoxLanguageArr(): array
    {
        return $this->checkBoxLanguage_arr;
    }

    /**
     * @return array
     */
    public function getCustomerGroupArr(): array
    {
        return $this->customerGroup_arr;
    }

    /**
     * @return array
     */
    public function getDisplayAtArr(): array
    {
        return $this->displayAt_arr;
    }

    /**
     * @return bool
     */
    public function getHasLink(): bool
    {
        return $this->nLink;
    }
}
