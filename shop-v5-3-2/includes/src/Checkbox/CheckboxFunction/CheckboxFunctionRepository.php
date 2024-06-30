<?php declare(strict_types=1);

namespace JTL\Checkbox\CheckboxFunction;

use JTL\Abstracts\AbstractDBRepository;
use JTL\DataObjects\DomainObjectInterface;

/**
 * Class CheckboxFunctionRepository
 * @package JTL\Checkbox\CheckboxFunction
 */
class CheckboxFunctionRepository extends AbstractDBRepository
{
    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'tcheckboxfunktion';
    }

    /**
     * @return string
     */
    public function getKeyName(): string
    {
        return 'kCheckBoxFunktion';
    }

    /**
     * @inheritdoc
     */
    public function get(int $id): ?\stdClass
    {
        return $this->getDB()->getSingleObject(
            'SELECT *'
            . ' FROM ' . $this->getTableName()
            . ' WHERE ' . $this->getKeyName() . ' = :id',
            ['id' => $id]
        );
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
