<?php declare(strict_types=1);

namespace JTL\DataObjects;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * Class AbstractDomainObject
 * @package JTL\DataObjects
 */
abstract class AbstractDomainObject implements DomainObjectInterface
{
    /**
     * AbstractDomainObject constructor.
     */
    public function __construct(public array $modifiedKeys = [])
    {
    }

    /**
     * Will ship an array containing Keys and values of protected and public properties
     *
     * @param bool $deep
     * @param bool $serialize
     * @return array
     */
    public function toArray(bool $deep = false, bool $serialize = true): array
    {
        $reflect = new ReflectionClass($this);
        if ($deep === true) {
            $properties = $reflect->getProperties();
        } else {
            $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        }
        $toArray = [];
        foreach ($properties as $property) {
            $propertyName  = $property->getName();
            $propertyValue = $property->getValue($this);
            if ($propertyName === 'modifiedKeys') {
                continue;
            }
            if ($serialize && (\is_array($propertyValue || \is_object($propertyValue)))) {
                $toArray[$propertyName] = \serialize($propertyValue);
            } else {
                $toArray[$propertyName] = $propertyValue;
            }
        }

        return $toArray;
    }

    /**
     * @param bool $tableColumns
     * @param bool $serialize
     * @return array
     */
    public function toArrayMapped(bool $tableColumns = true, bool $serialize = true): array
    {
        if ($tableColumns === true && \method_exists($this, 'getColumnMapping')) {
            $columnMap = $this->getColumnMapping();
        } else {
            return $this->toArray();
        }
        $reflect        = new ReflectionClass($this);
        $properties     = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $toArray        = [];
        $primaryKeyName = \method_exists($this, 'getPrimaryKey') ? $this->getPrimaryKey() : null;
        foreach ($properties as $property) {
            $propertyName  = $property->getName();
            $propertyValue = $property->getValue($this);
            if ($propertyName === 'modifiedKeys') {
                continue;
            }
            if (($propertyName === $primaryKeyName || $primaryKeyName === $columnMap[$propertyName])
                && (int)$propertyValue === 0) {
                continue;
            }
            if ($tableColumns) {
                $propertyName = $columnMap[$propertyName];
            }
            if ($serialize && (\is_array($propertyValue || \is_object($propertyValue)))) {
                $toArray[$propertyName] = \serialize($propertyValue);
            } else {
                $toArray[$propertyName] = $propertyValue;
            }
        }

        return $toArray;
    }

    /**
     * @param bool $deep
     * @return object
     */
    public function toObject(bool $deep = false): object
    {
        return (object)$this->toArray($deep);
    }

    /**
     * @param bool $tableColumns
     * @return object
     */
    public function toObjectMapped(bool $tableColumns = true): object
    {
        return (object)$this->toArrayMapped($tableColumns);
    }

    /**
     * @param bool     $deep
     * @param int|null $flags
     * @return string
     */
    public function toJson(bool $deep = false, ?int $flags = null): string
    {
        return \json_encode($this->toArray($deep), $flags ?? 0);
    }

    /**
     * if $useReverseMapping is true the array shipped will use mapped class properties
     * @param bool $useReverseMapping
     * @return array
     */
    public function extract(bool $useReverseMapping = false): array
    {
        $reflect    = new ReflectionClass($this);
        $attributes = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $extracted  = [];
        foreach ($attributes as $attribute) {
            $method = 'get' . \ucfirst($attribute->getName());
            if ($attribute->name !== 'modifiedKeys') {
                $extracted[$attribute->name] = $this->$method();
            }
        }

        return $extracted;
    }

    /**
     * @param string|null $domainObjectName
     * @return array
     * @throws ReflectionException
     */
    public static function getDefaultValues(?string $domainObjectName = null): array
    {
        $result         = [];
        $reflectedClass = new ReflectionClass($domainObjectName ?? static::class);
        foreach ($reflectedClass->getProperties() as $property) {
            $type = $property->getType();
            if ($type !== null) {
                $result[$property->getName()] = $type;
            }
        }

        return $result;
    }

    /**
     * @param array $newData
     * @return $this
     * @description Makes a copy of a readonly domain object while changing the values of the given keys.
     * @comment This is a workaround for the fact that readonly objects cannot be modified. A modified domain object
     *  should never be trusted without further checking.
     * @since 5.3.0
     */
    public function copyWith(array $newData): self
    {
        $asArray = $this->toArray(true);

        if (isset($asArray['modifiedKeys'])) {
            unset($asArray['modifiedKeys']);
        }
        foreach ($newData as $key => $value) {
            if (\array_key_exists($key, $asArray) === false) {
                throw new \RuntimeException('Attempting to modify a nonexistent key ('
                    . $key . ') in ' . static::class);
            }
            $asArray[$key]             = $value;
            $asArray['modifiedKeys'][] = $key;
        }

        return new static(...$asArray);
    }
}
