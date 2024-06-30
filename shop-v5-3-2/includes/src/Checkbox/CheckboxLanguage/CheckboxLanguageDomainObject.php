<?php declare(strict_types=1);

namespace JTL\Checkbox\CheckboxLanguage;

use JTL\DataObjects\AbstractDomainObject;
use JTL\DataObjects\DataTableObjectInterface;

/**
 * Class CheckboxLanguageDomainObject
 * @package JTL\Checkbox\CheckboxLanguage
 */
class CheckboxLanguageDomainObject extends AbstractDomainObject implements DataTableObjectInterface
{
    /**
     * @var string
     */
    private string $primaryKey;

    public function __construct(
        protected readonly int    $checkboxID,
        protected readonly int    $checkboxLanguageID,
        protected readonly int    $languageID,
        private readonly string   $iso,
        protected readonly string $text,
        protected readonly string $description,
        array $modifiedKeys = []
    ) {
        $this->primaryKey = 'kCheckBoxSprache';

        parent::__construct($modifiedKeys);
    }

    /**
     * @return array
     */
    private function getMappingArray(): array
    {
        return [
            'checkboxLanguageID' => 'checkboxLanguageID',
            'checkboxID'         => 'checkboxID',
            'languageID'         => 'languageID',
            'text'               => 'text',
            'description'        => 'description',
            'ISO'                => 'ISO,'
        ];
    }

    /**
     * @return array
     */
    private function getColumnMappingArray(): array
    {
        return [
            'kCheckBoxSprache' => 'checkboxLanguageID',
            'kCheckBox'        => 'checkboxID',
            'kSprache'         => 'languageID',
            'cText'            => 'text',
            'cBeschreibung'    => 'description',
            'modifiedKeys'     => 'modifiedKeys',
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
        return $this->{$this->getPrimaryKey()};
    }

    /**
     * @return int
     */
    public function getCheckboxLanguageID(): int
    {
        return $this->checkboxLanguageID;
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
    public function getLanguageID(): int
    {
        return $this->languageID;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getIso(): string
    {
        return $this->iso;
    }
}
