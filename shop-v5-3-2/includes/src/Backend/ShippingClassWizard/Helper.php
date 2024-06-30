<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace JTL\Backend\ShippingClassWizard;

use Illuminate\Support\Collection;
use JsonException;
use JTL\DB\DbInterface;
use JTL\Router\Controller\Backend\ShippingMethodsController;

/**
 * Class ShippingMethodHelper
 * @package JTL\Backend
 */
final class Helper
{
    /** @var DbInterface */
    private DbInterface $db;

    /** @var Builder */
    private Builder $builder;

    /** @var int|null */
    private static ?int $shippingClassCount = null;

    /** @var ShippingMethodsController */
    private ShippingMethodsController $controller;

    /**
     * Helper constructor
     * @param DbInterface               $db
     * @param ShippingMethodsController $controller
     */
    private function __construct(DbInterface $db, ShippingMethodsController $controller)
    {
        $this->db         = $db;
        $this->controller = $controller;
        $shippingClassIds = \array_map('\intval', \array_column($db->getObjects(
            'SELECT kVersandklasse AS id FROM tversandklasse'
        ), 'id'));
        $this->builder    = new Builder($shippingClassIds);
    }

    /**
     * @param DbInterface               $db
     * @param ShippingMethodsController $controller
     * @return self
     */
    public static function instance(DbInterface $db, ShippingMethodsController $controller): self
    {
        return new self($db, $controller);
    }

    /**
     * @return int
     */
    public function getShippingClassCount(): int
    {
        if (self::$shippingClassCount === null) {
            self::$shippingClassCount = $this->db->getSingleInt(
                'SELECT COUNT(kVersandklasse) AS classCount FROM tversandklasse',
                'classCount'
            );
        }

        return self::$shippingClassCount;
    }

    /**
     * @param int    $shippingMethodID
     * @param string $classIds
     * @return Definition
     */
    public function loadDefinition(int $shippingMethodID, string $classIds = ''): Definition
    {
        $wizard = $this->db->getSingleObject(
            'SELECT kVersandart, definition, result_hash
                FROM shipping_class_wizard
                WHERE kVersandart = :id',
            [
                'id' => $shippingMethodID,
            ]
        );

        if ($wizard === null) {
            return Definition::createEmpty($classIds);
        }

        $definition = $wizard->definition ?? 'a:1:{s:12:"combinations";s:3:"all";}';
        try {
            return Definition::jsonDecode($definition, $wizard->result_hash);
        } catch (JsonException) {
            return Definition::createEmpty($classIds);
        }
    }

    /**
     * @param int        $shippingMethodID
     * @param Definition $shippingMethodDefinition
     * @return void
     * @throws JsonException
     */
    public function saveDefinition(int $shippingMethodID, Definition $shippingMethodDefinition): void
    {
        $this->db->upsert('shipping_class_wizard', (object)[
            'kVersandart' => $shippingMethodID,
            'definition'  => \json_encode($shippingMethodDefinition, JSON_THROW_ON_ERROR),
            'result_hash' => $shippingMethodDefinition->getResultHash(),
        ]);
    }

    /**
     * @return Collection
     */
    public function getNamedShippingClasses(): Collection
    {
        return $this->db->getCollection(
            'SELECT kVersandklasse, cName
                FROM tversandklasse
                ORDER BY cName'
        );
    }

    /**
     * @param int $methodId
     * @return object|null
     */
    public function getShippingMethod(int $methodId): ?object
    {
        return $this->db->getSingleObject(
            'SELECT kVersandart, cName, cVersandklassen
                FROM tversandart
                WHERE kVersandart = :id',
            [
                'id' => $methodId,
            ]
        );
    }

    /**
     * @param string $shippingClasses
     * @return string
     */
    public function createResultHash(string $shippingClasses): string
    {
        return md5(\trim($shippingClasses));
    }

    /**
     * @param string $shippingClasses
     * @return array
     */
    public function getActiveShippingClassesOverview(string $shippingClasses): array
    {
        return $this->controller->getActiveShippingClassesOverview($shippingClasses);
    }

    /**
     * @param Definition $definition
     * @return string
     */
    public function buildShippingClasses(Definition $definition): string
    {
        switch ($definition->getCombinationType()) {
            case CombineTypes::ALL:
                return '-1';
            case CombineTypes::COMBINE_SINGLE:
                $combis = $definition->isLogicAnd()
                    ? $this->builder->combineSingleAnd($definition->getAllClassDefinitions())
                    : $this->builder->combineSingleOr($definition->getAllClassDefinitions());
                break;
            case CombineTypes::COMBINE_ALL:
                $combis = $definition->isLogicAnd()
                    ? $this->builder->combineAllAnd($definition->getAllClassDefinitions())
                    : $this->builder->combineAllOr($definition->getAllClassDefinitions());
                break;
            case CombineTypes::EXCLUSIVE:
                $combis = $definition->isLogicAnd()
                    ? $this->builder->exclusiveAnd($definition->getAllClassDefinitions())
                    : $this->builder->exclusiveOr($definition->getAllClassDefinitions());
                break;
            default:
                $combis = new Collection();
        }

        return $definition->isInverted()
            ? $this->builder->invert($combis)->implode(' ')
            : $combis->implode(' ');
    }
}
