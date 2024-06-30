<?php declare(strict_types=1);

namespace JTL\Abstracts;

use JTL\DataObjects\DomainObjectInterface;
use JTL\DB\DbInterface;
use JTL\Interfaces\RepositoryInterface;
use JTL\Shop;
use stdClass;

/**
 * Class AbstractRepository
 *
 * @package JTL\Abstracts
 */
abstract class AbstractDBRepository implements RepositoryInterface
{
    protected const UPDATE_OR_UPSERT_FAILED = -1;

    protected const DELETE_FAILED = -1;

    protected readonly DbInterface $db;

    /**
     * @param DbInterface|null $db
     */
    public function __construct(?DbInterface $db = null)
    {
        $this->db = $db ?? Shop::Container()->getDB();
    }

    /**
     * @return DbInterface
     * @comment Why do we need a protected Getter when the property is already protected?
     */
    protected function getDB(): DbInterface
    {
        return $this->db;
    }

    /**
     * @inheritdoc
     */
    abstract public function getTableName(): string;

    /**
     * @return string
     */
    public function getKeyName(): string
    {
        return 'id';
    }

    /**
     * @param DomainObjectInterface $domainObject
     * @return array
     * @comment Useful when joining tables in a query and unique column names are needed
     */
    public function getColumnMapping(DomainObjectInterface $domainObject): array
    {
        $columnMapping = [];
        foreach (\array_keys($domainObject->getDefaultValues($domainObject::class)) as $name) {
            $columnMapping[$this->getTableName() . \ucfirst($name)] = $name;
        }

        return $columnMapping;
    }

    /**
     * @inheritdoc
     */
    public function getKeyValue(DomainObjectInterface $domainObject): ?int
    {
        $keyName = $this->getKeyName();

        return $domainObject->$keyName ?? null;
    }

    /**
     * @param int $id
     * @return stdClass|null
     */
    public function get(int $id): ?stdClass
    {
        return $this->db->select($this->getTableName(), $this->getKeyName(), $id);
    }

    /**
     * @param array $filters
     * @return stdClass|null
     */
    public function filter(array $filters): ?stdClass
    {
        $keys      = \array_keys($filters);
        $keyValues = \array_values($filters);
        if ($keys === [] || $keyValues === []) {
            return null;
        }
        return $this->db->select($this->getTableName(), $keys, $keyValues);
    }

    /**
     * @inheritdoc
     */
    public function getList(array $filters): array
    {
        $keys      = \array_keys($filters);
        $keyValues = \array_values($filters);
        if ($keys === [] || $keyValues === []) {
            return [];
        }

        return $this->db->selectAll(
            $this->getTableName(),
            $keys,
            $keyValues
        );
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return ($this->db->deleteRow(
            $this->getTableName(),
            $this->getKeyName(),
            $id
        ) !== self::DELETE_FAILED);
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

        $obj = $domainObject->toObject();
        foreach ($obj as &$value) {
            if ($value === null) {
                $value = '_DBNULL_';
            }
        }

        return $this->db->insertRow($this->getTableName(), $obj);
    }

    /**
     * @inheritdoc
     */
    public function update(DomainObjectInterface $domainObject): bool
    {
        if (isset($domainObject->modifiedKeys) && \count($domainObject->modifiedKeys) > 0) {
            throw new \InvalidArgumentException('DomainObject has been modified. The modified keys are '
                . \print_r($domainObject->modifiedKeys, true) . '. The DomainObject looks like this: '
                . \print_r($domainObject->toArray(true), true));
        }

        return ($this->db->updateRow(
            $this->getTableName(),
            $this->getKeyName(),
            $this->getKeyValue($domainObject),
            $domainObject->toObject()
        ) !== self::UPDATE_OR_UPSERT_FAILED);
    }

    /**
     * @param array $values
     * @return int[]
     */
    final protected function ensureIntValuesInArray(array $values): array
    {
        return \array_map('\intval', $values);
    }
}
