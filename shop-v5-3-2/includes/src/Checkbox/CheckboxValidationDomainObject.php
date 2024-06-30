<?php declare(strict_types=1);

namespace JTL\Checkbox;

use JTL\DataObjects\AbstractDomainObject;

/**
 * Class CheckboxValidationDomainObject
 * @package JTL\Checkbox
 */
class CheckboxValidationDomainObject extends AbstractDomainObject
{
    public function __construct(
        protected readonly int $customerGroupId = 0,
        protected readonly int $location = 0,
        protected readonly bool $active = false,
        protected readonly bool $logging = false,
        protected readonly bool $language = false,
        protected readonly bool $special = false,
        protected readonly bool $hasDownloads = false,
        array $modifiedKeys = []
    ) {
        parent::__construct($modifiedKeys);
    }

    /**
     * @return array
     */
    private function getMappingArray(): array
    {
        return [
            'customerGroupId' => 'customerGroupId',
            'kKundengruppe'   => 'customerGroupId',
            'location'        => 'location',
            'language'        => 'language',
            'active'          => 'active',
            'logging'         => 'logging',
            'special'         => 'special',
            'modifiedKeys'    => 'modifiedKeys',
        ];
    }

    /**
     * @return array
     */
    private function getColumnMappingArray(): array
    {
        return [];
    }

    /**
     * @var string[]
     */
    private array $mapping;

    /**
     * @var string[]
     */
    private array $columnMapping;

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
    public function getCustomerGroupId(): int
    {
        return $this->customerGroupId;
    }

    /**
     * @return int
     */
    public function getLocation(): int
    {
        return $this->location;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function getLogging(): bool
    {
        return $this->logging;
    }

    /**
     * @return bool
     */
    public function getSpecial(): bool
    {
        return $this->special;
    }

    /**
     * @return bool
     */
    public function getHasDownloads(): bool
    {
        return $this->hasDownloads;
    }

    /**
     * @return bool
     */
    public function getLanguage(): bool
    {
        return $this->language;
    }
}
