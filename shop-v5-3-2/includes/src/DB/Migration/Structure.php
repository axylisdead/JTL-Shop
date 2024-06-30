<?php declare(strict_types=1);

namespace JTL\DB\Migration;

use InvalidArgumentException;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Session\Backend;
use JTLShop\SemVer\Parser;
use stdClass;
use Systemcheck\Platform\DBServerInfo;
use function Functional\first;

class Structure
{
    public function __construct(
        private readonly DbInterface $db,
        private readonly JTLCacheInterface $cache,
        private readonly Info $info
    ) {
    }

    /**
     * @param bool $extended
     * @param bool $clearCache
     * @return array
     */
    public function getDBStruct(bool $extended = false, bool $clearCache = false): array
    {
        $check           = new Check($this->db);
        static $dbStruct = [
            'normal'   => null,
            'extended' => null,
        ];

        $dbLocked    = null;
        $database    = $this->db->getConfig()['database'];
        $versionInfo = $this->info->getDBServerInfo();

        if ($clearCache) {
            if ($this->cache->isActive()) {
                $this->cache->flushTags([\CACHING_GROUP_CORE . '_getDBStruct']);
            } else {
                Backend::set('getDBStruct_extended', false);
                Backend::set('getDBStruct_normal', false);
            }
            $dbStruct['extended'] = null;
            $dbStruct['normal']   = null;
        }

        if ($extended) {
            $cacheID = 'getDBStruct_extended';
            if ($dbStruct['extended'] === null) {
                $dbStruct['extended'] = $this->cache->isActive()
                    ? $this->cache->get($cacheID)
                    : Backend::get($cacheID, false);
            }
            $dbStructure =& $dbStruct['extended'];

            if ($versionInfo->isSupportedVersion() >= DBServerInfo::SUPPORTED) {
                $dbLocked = [];
                $dbStatus = $this->db->getObjects(
                    'SHOW OPEN TABLES
                    WHERE `Database` LIKE :schema',
                    ['schema' => $database]
                );
                if ($dbStatus) {
                    foreach ($dbStatus as $oStatus) {
                        if ((int)$oStatus->In_use > 0) {
                            $dbLocked[$oStatus->Table] = 1;
                        }
                    }
                }
            }
        } else {
            $cacheID = 'getDBStruct_normal';
            if ($dbStruct['normal'] === null) {
                $dbStruct['normal'] = $this->cache->isActive()
                    ? $this->cache->get($cacheID)
                    : Backend::get($cacheID);
            }
            $dbStructure =& $dbStruct['normal'];
        }

        if ($dbStructure === false) {
            $dbStructure = [];
            $dbData      = $check->getTableStructure();

            foreach ($dbData as $data) {
                $table = $data->TABLE_NAME;
                if ($extended) {
                    $dbStructure[$table]            = $data;
                    $dbStructure[$table]->Columns   = [];
                    $dbStructure[$table]->Migration = Check::MIGRATE_NONE;
                    if ($dbLocked === null) {
                        $dbStructure[$table]->Locked = \str_contains($data->TABLE_COMMENT, ':Migrating') ? 1 : 0;
                    } else {
                        $dbStructure[$table]->Locked = $dbLocked[$table] ?? 0;
                    }
                } else {
                    $dbStructure[$table] = [];
                }

                $columns = $this->db->getObjects(
                    'SELECT `COLUMN_NAME`, `DATA_TYPE`, `COLUMN_TYPE`, `CHARACTER_SET_NAME`, `COLLATION_NAME`
                        FROM information_schema.COLUMNS
                        WHERE `TABLE_SCHEMA` = :schema
                            AND `TABLE_NAME` = :table
                        ORDER BY `ORDINAL_POSITION`',
                    [
                        'schema' => $database,
                        'table'  => $table
                    ]
                );
                foreach ($columns as $column) {
                    if ($extended) {
                        $dbStructure[$table]->Columns[$column->COLUMN_NAME] = $column;
                    } else {
                        $dbStructure[$table][] = $column->COLUMN_NAME;
                    }
                }
                if ($extended) {
                    $dbStructure[$table]->Migration = $check->getNeededMigrationsForTable($data);
                }
            }
            if ($this->cache->isActive()) {
                $this->cache->set(
                    $cacheID,
                    $dbStructure,
                    [\CACHING_GROUP_CORE, \CACHING_GROUP_CORE . '_getDBStruct']
                );
            } else {
                Backend::set($cacheID, $dbStructure);
            }
        } elseif ($extended) {
            foreach (\array_keys($dbStructure) as $table) {
                $dbStructure[$table]->Locked = $dbLocked[$table] ?? 0;
            }
        }

        return $dbStructure;
    }

    /**
     * @return array
     */
    public function getDBFileStruct(): array
    {
        $version    = Parser::parse(\APPLICATION_VERSION);
        $versionStr = $version->getMajor() . '-' . $version->getMinor() . '-' . $version->getPatch();
        if ($version->hasPreRelease()) {
            $preRelease  = $version->getPreRelease();
            $versionStr .= '-' . $preRelease->getGreek();
            if ($preRelease->getReleaseNumber() > 0) {
                $versionStr .= '-' . $preRelease->getReleaseNumber();
            }
        }

        $fileList = \PFAD_ROOT . \PFAD_ADMIN . \PFAD_INCLUDES . \PFAD_SHOPMD5 . 'dbstruct_' . $versionStr . '.json';
        if (!\file_exists($fileList)) {
            throw new InvalidArgumentException(\sprintf(\__('errorReadStructureFile'), $fileList));
        }
        try {
            $struct = \json_decode(\file_get_contents($fileList), false, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $struct = null;
        }

        return \is_object($struct) ? \get_object_vars($struct) : [];
    }

    /**
     * @param string $msg
     * @param bool   $engineError
     * @return stdClass
     * @since 5.2.0
     */
    public function createDBStructError(string $msg, bool $engineError = false): stdClass
    {
        return (object)[
            'errMsg'        => $msg,
            'isEngineError' => $engineError,
        ];
    }

    /**
     * @param array $dbFileStruct
     * @param array $dbStruct
     * @return object[]
     * @since 5.2.0
     */
    public function compareDBStruct(array $dbFileStruct, array $dbStruct): array
    {
        $errors = [];
        foreach ($dbFileStruct as $table => $columns) {
            if (!\array_key_exists($table, $dbStruct)) {
                $errors[$table] = $this->createDBStructError(\__('errorNoTable'));
                continue;
            }
            if ($dbStruct[$table]->Migration > Check::MIGRATE_NONE) {
                $errors[$table] = $this->createDBStructError(
                    $this->getStructErrorText($dbStruct[$table]),
                    true
                );
                continue;
            }

            foreach ($columns as $column) {
                if (!\in_array($column, isset($dbStruct[$table]->Columns)
                    ? \array_keys($dbStruct[$table]->Columns)
                    : $dbStruct[$table], true)
                ) {
                    $errors[$table] = $this->createDBStructError(\__('errorRowMissing', $column, $table));
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * @param stdClass $tableData
     * @return string
     */
    public function getStructErrorText(stdClass $tableData): string
    {
        $check = new Check($this->db);
        if (($tableData->Migration & Check::MIGRATE_TABLE) > Check::MIGRATE_NONE) {
            $tableMigration = $check->getFirstMigration($tableData->Migration);

            return \__('errorMigrationTable_' . $tableMigration, $tableData->TABLE_NAME);
        }
        if (($tableData->Migration & Check::MIGRATE_COLUMN) > Check::MIGRATE_NONE) {
            $tableMigration = $check->getFirstMigration($tableData->Migration);
            $column         = first($check->getColumnsNeedingMigrationsForTable($tableData->TABLE_NAME));

            return \__('errorMigrationTable_' . $tableMigration, $column->COLUMN_NAME);
        }

        return '';
    }
}
