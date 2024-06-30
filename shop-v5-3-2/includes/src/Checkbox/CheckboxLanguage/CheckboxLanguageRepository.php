<?php declare(strict_types=1);

namespace JTL\Checkbox\CheckboxLanguage;

use JTL\Abstracts\AbstractDBRepository;
use JTL\DataObjects\DomainObjectInterface;

/**
 * Class CheckboxLanguageRepository
 * @package JTL\Checkbox\CheckboxLanguage
 */
class CheckboxLanguageRepository extends AbstractDBRepository
{
    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'tcheckboxsprache';
    }

    /**
     * @return string
     */
    public function getKeyName(): string
    {
        return 'kCheckBoxSprache';
    }

    /**
     * @inheritdoc
     */
    public function update(DomainObjectInterface $domainObject, int $ID = 0): bool
    {
        if ($ID === 0) {
            return false;
        }
        $obj = $domainObject->toObjectMapped(true);
        unset($obj->modifiedKeys);

        return ($this->getDB()->updateRow(
            $this->getTableName(),
            $this->getKeyName(),
            $ID,
            $obj
        ) !== self::UPDATE_OR_UPSERT_FAILED
        );
    }

    public function getLanguagesByCheckboxID(int $ID): array
    {
        $stmt = '
            SELECT 
                cbl.kCheckBoxSprache,
                cbl.kCheckBox,
                cbl.kSprache,
                cbl.cText,
                cbl.cBeschreibung,
                l.cISO ISO
            FROM
                tcheckboxsprache cbl
                    JOIN
                tsprache l ON cbl.kSprache = l.kSprache
            WHERE
                kCheckbox = :checkboxID';

        return $this->db->getArrays($stmt, ['checkboxID' => $ID]);
    }

    /**
     * @inheritdoc
     */
    public function insert(DomainObjectInterface $domainObject): int
    {
        if (isset($domainObject->modifiedKeys) && \count($domainObject->modifiedKeys) > 0) {
            throw new \InvalidArgumentException('DomainObject has been modified. The last modified keys are '
                . \print_r($domainObject->modifiedKeys, true) . '. The DomainObject looks like this: '
                . \print_r($domainObject->toArray(true), true));
        }

        $obj = $domainObject->toObjectMapped();
        foreach ($obj as &$value) {
            if ($value === null) {
                $value = '_DBNULL_';
            }
        }

        return $this->db->insertRow($this->getTableName(), $obj);
    }
}
