<?php declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use InvalidArgumentException;
use JTL\Backend\Permissions;
use JTL\Backend\Status;
use JTL\DB\Migration\Check;
use JTL\DB\Migration\Info;
use JTL\DB\Migration\InnoDB;
use JTL\DB\Migration\Structure;
use JTL\Exceptions\PermissionException;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Systemcheck\Platform\DBServerInfo;
use function Functional\every;

/**
 * Class DBCheckController
 * @package JTL\Router\Controller\Backend
 */
class DBCheckController extends AbstractBackendController
{
    /**
     * @inheritdoc
     * @throws PermissionException
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::DBCHECK_VIEW);
        $this->getText->loadAdminLocale('pages/dbcheck');
        $this->cache->flush(Status::CACHE_ID_DATABASE_STRUCT);
        $info              = new Info($this->db);
        $struct            = new Structure($this->db, $this->cache, $info);
        $errorMsg          = '';
        $dbErrors          = [];
        $maintenanceResult = null;
        $engineUpdate      = null;
        $fulltextIndizes   = null;
        $valid             = Form::validateToken();
        try {
            $dbFileStruct = $struct->getDBFileStruct();
        } catch (InvalidArgumentException $e) {
            $errorMsg     = $e->getMessage();
            $dbFileStruct = [];
        }
        if ($valid && Request::postVar('update') === 'script') {
            $scriptName = 'innodb_and_utf8_update_'
                . \str_replace('.', '_', $this->db->getConfig()['host']) . '_'
                . $this->db->getConfig()['database'] . '_'
                . \date('YmdHis') . '.sql';

            return new TextResponse(
                $this->doEngineUpdateScript($scriptName, \array_keys($dbFileStruct)),
                200,
                ['Content-Disposition' => 'attachment; filename="' . $scriptName . '"']
            );
        }
        $dbStruct = $struct->getDBStruct(true, true);

        if ($valid && !empty($_POST['action']) && !empty($_POST['check'] && \count($dbFileStruct) > 0)) {
            $ok                = every($_POST['check'], function ($elem) use ($dbFileStruct): bool {
                return \array_key_exists($elem, $dbFileStruct);
            });
            $maintenanceResult = $ok ? $this->doDBMaintenance($_POST['action'], $_POST['check']) : false;
        }

        if ($errorMsg === '') {
            $dbErrors = $struct->compareDBStruct($dbFileStruct, $dbStruct);
        }

        if (\count($dbErrors) > 0) {
            $engineErrors = \array_filter($dbErrors, static function ($item) {
                return $item->isEngineError;
            });
            if (\count($engineErrors) > 5) {
                $engineUpdate    = $this->determineEngineUpdate($dbStruct);
                $fulltextIndizes = $info->getFulltextIndizes();
            }
        }
        $this->alertService->addError($errorMsg, 'errorDBCheck');

        return $smarty->assign('cDBFileStruct_arr', $dbFileStruct)
            ->assign('cDBStruct_arr', $dbStruct)
            ->assign('cDBError_arr', $dbErrors)
            ->assign('maintenanceResult', $maintenanceResult)
            ->assign('scriptGenerationAvailable', \ADMIN_MIGRATION)
            ->assign('tab', isset($_REQUEST['tab']) ? Text::filterXSS($_REQUEST['tab']) : '')
            ->assign('DB_Version', $info->getDBServerInfo())
            ->assign('FulltextIndizes', $fulltextIndizes)
            ->assign('engineUpdate', $engineUpdate)
            ->assign('route', $this->route)
            ->getResponse('dbcheck.tpl');
    }

    /**
     * @param string $action
     * @param array  $tables
     * @return stdClass[]|false
     */
    private function doDBMaintenance(string $action, array $tables): array|bool
    {
        $cmd = match ($action) {
            'optimize' => 'OPTIMIZE TABLE ',
            'analyze'  => 'ANALYZE TABLE ',
            'repair'   => 'REPAIR TABLE ',
            'check'    => 'CHECK TABLE ',
            default    => false
        };

        return \count($tables) > 0 && $cmd !== false
            ? $this->db->getObjects($cmd . \implode(', ', $tables))
            : false;
    }

    /**
     * @param array $dbStruct
     * @return stdClass
     */
    private function determineEngineUpdate(array $dbStruct): stdClass
    {
        $result             = new stdClass();
        $result->tableCount = 0;
        $result->dataSize   = 0;
        foreach ($dbStruct as $meta) {
            if (isset($meta->Migration) && $meta->Migration !== Check::MIGRATE_NONE) {
                $result->tableCount++;
                $result->dataSize += $meta->DATA_SIZE;
            }
        }

        $result->estimated = [
            $result->tableCount * 1.60 + $result->dataSize / 1048576 * 1.15,
            $result->tableCount * 2.40 + $result->dataSize / 1048576 * 2.50,
        ];

        return $result;
    }

    /**
     * @param string   $fileName
     * @param string[] $shopTables
     * @return string
     */
    private function doEngineUpdateScript(string $fileName, array $shopTables): string
    {
        $nl = "\r\n";

        $database    = $this->db->getConfig()['database'];
        $host        = $this->db->getConfig()['host'];
        $info        = new Info($this->db);
        $check       = new Check($this->db);
        $structure   = new Structure($this->db, $this->cache, $info);
        $innodb      = new InnoDB($this->db, $info, $check, $structure, Shop::Container()->getGetText());
        $versionInfo = $info->getDBServerInfo();
        $recreateFKs = '';

        $result  = '-- ' . $fileName . $nl;
        $result .= '-- ' . $nl;
        $result .= '-- @host: ' . $host . $nl;
        $result .= '-- @database: ' . $database . $nl;
        $result .= '-- @created: ' . \date(\DATE_RFC822) . $nl;
        $result .= '-- ' . $nl;
        $result .= '-- @important: !!! PLEASE MAKE A BACKUP OF STRUCTURE AND DATA FOR `' . $database . '` !!!' . $nl;
        $result .= '-- ' . $nl;
        $result .= $nl;
        $result .= '-- ---------------------------------------------------------'
            . '-------------------------------------------' . $nl;
        $result .= '-- ' . $nl;
        $result .= 'use `' . $database . '`;' . $nl;
        $result .= 'set SQL_MODE = \'\';' . $nl;

        foreach ($check->getTablesNeedingMigration() as $table) {
            $fulltextSQL = [];
            $migration   = $check->getNeededMigrationsForTable($table);

            if (!\in_array($table->TABLE_NAME, $shopTables, true)) {
                continue;
            }

            if ($versionInfo->isSupportedVersion() < DBServerInfo::SUPPORTED) {
                // Fulltext indizes are not supported for innoDB on MySQL < 5.6
                $fulltextIndizes = $info->getFulltextIndizes($table->TABLE_NAME);

                if ($fulltextIndizes) {
                    $result .= $nl . '--' . $nl;
                    $result .= '-- remove fulltext indizes because there is no support for innoDB on MySQL < 5.6 '
                        . $nl;
                    foreach ($fulltextIndizes as $fulltextIndex) {
                        $fulltextSQL[] = /** @lang text */
                            'ALTER TABLE `' . $table->TABLE_NAME . '` DROP KEY `' . $fulltextIndex->INDEX_NAME . '`';
                    }
                }
            }

            if (($migration & Check::MIGRATE_TABLE) !== Check::MIGRATE_NONE) {
                $result .= $nl . '--' . $nl;
                if (($migration & Check::MIGRATE_TABLE) === Check::MIGRATE_TABLE) {
                    $result .= '-- migrate engine and collation for ' . $table->TABLE_NAME . $nl;
                } elseif (($migration & Check::MIGRATE_INNODB) === Check::MIGRATE_INNODB) {
                    $result .= '-- migrate engine for ' . $table->TABLE_NAME . $nl;
                } elseif (($migration & Check::MIGRATE_UTF8) === Check::MIGRATE_UTF8) {
                    $result .= '-- migrate collation for ' . $table->TABLE_NAME . $nl;
                }
            } else {
                $result .= $nl;
            }

            if (\count($fulltextSQL) > 0) {
                $result .= \implode(';' . $nl, $fulltextSQL) . ';' . $nl;
            }

            $sql    = $innodb->sqlMoveToInnoDB($table);
            $fkSQLs = $info->getForeignKeyStatements($table->TABLE_NAME, true);
            if (!empty($sql)) {
                $result .= '--' . $nl;
                foreach ($fkSQLs->dropFK as $fkSQL) {
                    $result .= $fkSQL . ';' . $nl;
                }
                $result .= $sql . ';' . $nl;
                foreach ($fkSQLs->createFK as $fkSQL) {
                    $recreateFKs .= $fkSQL . ';' . $nl;
                }
            }

            $sql = $innodb->sqlConvertUTF8($table, $nl);
            if (!empty($sql)) {
                $result .= '--' . $nl;
                $result .= '-- migrate collation and / or datatype for columns in ' . $table->TABLE_NAME . $nl;
                $result .= '--' . $nl;
                $result .= $sql . ';' . $nl;
            }
        }

        $result .= $nl;

        if ($versionInfo->isSupportedVersion() < DBServerInfo::SUPPORTED) {
            // Fulltext search is not available on MySQL < 5.6
            $result .= '--' . $nl;
            $result .= '-- Fulltext search is not available on MySQL < 5.6' . $nl;
            $result .= '--' . $nl;
            $result .= "UPDATE `teinstellungen` SET `cWert` = 'N' WHERE `cName` = 'suche_fulltext';" . $nl;
            $result .= $nl;
        }

        if (!empty($recreateFKs)) {
            $result .= '--' . $nl;
            $result .= '-- Recreate foreign keys' . $nl;
            $result .= '--' . $nl;
            $result .= $recreateFKs;
            $result .= $nl;
        }

        return $result;
    }
}
