<?php declare(strict_types=1);

namespace JTL\Update;

use JTL\DB\DbInterface;
use JTL\DB\Migration\Check;
use JTL\DB\Migration\Info;
use JTL\DB\Migration\InnoDB;
use JTL\DB\Migration\Structure;
use JTL\Shop;
use stdClass;

/**
 * Class DBMigrationHelper
 * @package JTL\Update
 */
class DBMigrationHelper
{
    /** @deprecated since 5.3.0 */
    public const IN_USE  = InnoDB::IN_USE;
    /** @deprecated since 5.3.0 */
    public const SUCCESS = InnoDB::SUCCESS;
    /** @deprecated since 5.3.0 */
    public const FAILURE = InnoDB::FAILURE;

    /** @deprecated since 5.3.0 */
    public const MIGRATE_NONE = Check::MIGRATE_NONE;
    /** @deprecated since 5.3.0 */
    public const MIGRATE_INNODB = Check::MIGRATE_INNODB;
    /** @deprecated since 5.3.0 */
    public const MIGRATE_UTF8 = Check::MIGRATE_UTF8;
    /** @deprecated since 5.3.0 */
    public const MIGRATE_TEXT = Check::MIGRATE_C_TEXT;
    /** @deprecated since 5.3.0 */
    public const MIGRATE_C_UTF8 = Check::MIGRATE_C_UTF8;
    /** @deprecated since 5.3.0 */
    public const MIGRATE_TINYINT = Check::MIGRATE_C_TINYINT;
    /** @deprecated since 5.3.0 */
    public const MIGRATE_ROWFORMAT = Check::MIGRATE_ROWFORMAT;
    /** @deprecated since 5.3.0 */
    public const MIGRATE_TABLE     = Check::MIGRATE_TABLE;
    /** @deprecated since 5.3.0 */
    public const MIGRATE_COLUMN    = Check::MIGRATE_COLUMN;

    /** @var Info|null */
    private static ?Info $info = null;

    /** @var Check|null */
    private static ?Check $check = null;

    /** @var Structure|null */
    private static ?Structure $structure = null;

    /**
     * @param DbInterface|null $db
     * @return Info
     */
    private static function getInfo(?DbInterface $db = null): Info
    {
        return self::$info ?? (self::$info = new Info($db ?? Shop::Container()->getDB()));
    }

    /**
     * @param DbInterface|null $db
     * @return Check
     */
    private static function getCheck(?DbInterface $db = null): Check
    {
        $db ??= Shop::Container()->getDB();

        return self::$check ?? (self::$check = new Check($db));
    }

    /**
     * @param DbInterface|null $db
     * @return Structure
     */
    private static function getStructure(?DbInterface $db = null): Structure
    {
        $db ??= Shop::Container()->getDB();

        return self::$structure ?? (self::$structure = new Structure($db, Shop::Container()->getCache(), self::getInfo($db)));
    }

    /**
     * @param string $tableName
     * @return string - InnoDB::SUCCESS, InnoDB::FAILURE or InnoDB::IN_USE
     */
    public static function migrateToInnoDButf8(string $tableName): string
    {
        $db     = Shop::Container()->getDB();
        $innodb = new InnoDB(
            $db,
            self::getInfo($db),
            self::getCheck($db),
            self::getStructure($db),
            Shop::Container()->getGetText()
        );

        return $innodb->migrateToInnoDButf8($tableName);
    }

    /**
     * @return stdClass
     * @deprecated since 5.3.0
     */
    public static function getMySQLVersion(): stdClass
    {
        $db                  = Shop::Container()->getDB();
        $versionInfo         = new stdClass();
        $dbServerInfo        = self::getInfo($db)->getDBServerInfo();
        $versionInfo->server = $db->getServerInfo();
        $versionInfo->innodb = new stdClass();

        $versionInfo->innodb->support = $dbServerInfo->hasInnoDBSupport();
        /*
         * Since MariaDB 10.0, the default InnoDB implementation is based on InnoDB from MySQL 5.6.
         * Since MariaDB 10.3.7 and later, the InnoDB implementation has diverged substantially from the
         * InnoDB in MySQL and the InnoDB Version is no longer reported.
         */
        $versionInfo->innodb->version = $db->getSingleObject(
            "SHOW VARIABLES LIKE 'innodb_version'"
        )->Value ?? $dbServerInfo->getVersion();
        $versionInfo->innodb->size    = $dbServerInfo->getInnoDBSize();
        $versionInfo->collation_utf8  = $dbServerInfo->hasUTF8Support();

        return $versionInfo;
    }

    /**
     * @return stdClass[]
     * @deprecated since 5.3.0
     */
    public static function getTablesNeedMigration(): array
    {
        return self::getCheck()->getTablesNeedingMigration();
    }

    /**
     * @param DbInterface $db
     * @param string[]    $excludeTables
     * @return stdClass|null
     * @deprecated since 5.3.0
     */
    public static function getNextTableNeedMigration(DbInterface $db, array $excludeTables = []): ?stdClass
    {
        return self::getCheck($db)->getNextTableNeedingMigration($excludeTables);
    }

    /**
     * @param string $table
     * @return stdClass|null
     * @deprecated since 5.3.0
     */
    public static function getTable(string $table): ?stdClass
    {
        return self::getCheck()->getTableData($table);
    }

    /**
     * @param string|null $table
     * @return stdClass[]
     * @deprecated since 5.3.0
     */
    public static function getFulltextIndizes(?string $table = null): array
    {
        return self::getInfo()->getFulltextIndizes($table);
    }

    /**
     * @param string|stdClass $table
     * @return int
     * @deprecated since 5.3.0
     */
    public static function isTableNeedMigration($table): int
    {
        $tableData = \is_string($table) ? self::getCheck()->getTableData($table) : $table;

        return self::getCheck()->getNeededMigrationsForTable($tableData ?? (object)[]);
    }

    /**
     * @param DbInterface $db
     * @param string      $table
     * @return bool
     * @deprecated since 5.3.0
     */
    public static function isTableInUse(DbInterface $db, $table): bool
    {
        return self::getInfo($db)->isTableInUse(self::getCheck($db)->getTableData($table));
    }

    /**
     * @param string $table
     * @return stdClass[]
     * @deprecated since 5.3.0
     */
    public static function getColumnsNeedMigration(string $table): array
    {
        return self::getCheck()->getColumnsNeedingMigrationsForTable($table);
    }

    /**
     * @param string $table
     * @return stdClass[]
     * @deprecated since 5.3.0
     */
    public static function getFKDefinitions(string $table): array
    {
        return self::getInfo()->getForeignKeyDefinitions($table, true);
    }

    /**
     * @param stdClass $table
     * @return string
     * @deprecated since 5.3.0
     */
    public static function sqlAddLockInfo($table): string
    {
        return self::getInfo()->addTableLockInfo($table);
    }

    /**
     * @param stdClass $table
     * @return string
     * @deprecated since 5.3.0
     */
    public static function sqlClearLockInfo($table): string
    {
        return self::getInfo()->clearTableLockInfo($table);
    }

    /**
     * @param string $table
     * @return object - dropFK: Array with SQL to drop associated foreign keys,
     *                  createFK: Array with SQL to recreate them
     * @deprecated since 5.3.0
     */
    public static function sqlRecreateFKs(string $table): object
    {
        return self::getInfo()->getForeignKeyStatements($table, true);
    }

    /**
     * @param stdClass $table
     * @return string
     * @deprecated since 5.3.0
     */
    public static function sqlMoveToInnoDB($table): string
    {
        $db     = Shop::Container()->getDB();
        $innodb = new InnoDB($db, self::getInfo($db), self::getCheck($db), self::getStructure($db), Shop::Container()->getGetText());

        return $innodb->sqlMoveToInnoDB($table);
    }

    /**
     * @param stdClass $table
     * @param string   $lineBreak
     * @return string
     * @deprecated since 5.3.0
     */
    public static function sqlConvertUTF8(stdClass $table, string $lineBreak = ''): string
    {
        $db     = Shop::Container()->getDB();
        $innodb = new InnoDB(
            $db,
            self::getInfo($db),
            self::getCheck($db),
            self::getStructure($db),
            Shop::Container()->getGetText()
        );

        return $innodb->sqlConvertUTF8($table, $lineBreak);
    }

    /**
     * @param string $msg
     * @param bool   $engineError
     * @return stdClass
     * @deprecated since 5.3.0
     */
    public static function createDBStructError(string $msg, bool $engineError = false): stdClass
    {
        return self::getStructure()->createDBStructError($msg, $engineError);
    }

    /**
     * @param array $dbFileStruct
     * @param array $dbStruct
     * @return object[]
     * @deprecated since 5.3.0
     */
    public static function compareDBStruct(array $dbFileStruct, array $dbStruct): array
    {
        return self::getStructure()->compareDBStruct($dbFileStruct, $dbStruct);
    }

    /**
     * @param string $status
     * @param string $tableName
     * @param int    $step
     * @param array  $exclude
     * @return stdClass
     * @deprecated since 5.3.0
     */
    public static function doMigrateToInnoDB_utf8(
        string $status = 'start',
        string $tableName = '',
        int    $step = 1,
        array  $exclude = []
    ): stdClass {
        $db     = Shop::Container()->getDB();
        $innodb = new InnoDB(
            $db,
            self::getInfo($db),
            self::getCheck($db),
            self::getStructure($db),
            Shop::Container()->getGetText()
        );

        return $innodb->doMigrateToInnoDBUTF8($status, $tableName, $step, $exclude);
    }

    /**
     * @param bool $extended
     * @param bool $clearCache
     * @return array
     * @deprecated since 5.3.0
     */
    public static function getDBStruct(bool $extended = false, bool $clearCache = false): array
    {
        return self::getStructure()->getDBStruct($extended, $clearCache);
    }

    /**
     * @return array
     * @deprecated since 5.3.0
     */
    public static function getDBFileStruct(): array
    {
        return self::getStructure()->getDBFileStruct();
    }

    /**
     * @param stdClass $table
     * @return string
     * @deprecated since 5.3.0
     */
    public static function getStructErrorText(stdClass $table): string
    {
        $db     = Shop::Container()->getDB();
        $innodb = new InnoDB(
            $db,
            self::getInfo($db),
            self::getCheck($db),
            self::getStructure($db),
            Shop::Container()->getGetText()
        );

        return $innodb->getStructErrorText($table);
    }
}
