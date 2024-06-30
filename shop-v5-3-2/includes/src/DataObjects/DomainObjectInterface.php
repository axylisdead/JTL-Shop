<?php declare(strict_types=1);

namespace JTL\DataObjects;

/**
 * Interface DataObjectInterface
 * @package JTL\DataObjects
 */
interface DomainObjectInterface
{
    /**
     * Will return an array containing Keys and values of protected and public properties.
     * Shall use getColumnMapping() if $tableColumns = true
     *
     * @param bool $deep
     * @param bool $serialize
     * @return array
     */
    public function toArray(bool $deep = false, bool $serialize = true): array;

    /**
     * @param bool $useReverseMapping
     * @return array
     */
    public function extract(bool $useReverseMapping = false): array;

    /**
     * Creates and returns object from data provided in toArray()
     * @param bool $deep
     * @return object
     */
    public function toObject(bool $deep = false): object;
}
