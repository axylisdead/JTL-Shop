<?php declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Platform\DBServerInfo;
use Systemcheck\Tests\DBConnectionTest;

/**
 * Class PhpDBSQLMode
 * @package Systemcheck\Tests\Shop5
 */
class PhpDBSQLMode extends DBConnectionTest
{
    protected $name          = 'Datenbank-SQL Mode';
    protected $requiredState = 'off';
    protected $description   = 'Bestimmte Einstellungen für den SQL-Mode können zu fehlerhaftem Verhalten in JTL-Shop führen.';

    /**
     * @inheritdoc
     */
    protected function handleDBAvailable(): bool
    {
        $pdoDB = $this->getPdoDB();
        if ($pdoDB === null || !defined('DB_DEFAULT_SQL_MODE')) {
            return $this->handleNotSupported();
        }

        if (!\DB_DEFAULT_SQL_MODE) {
            $this->currentState = $this->requiredState;

            return true;
        }

        $version = new DBServerInfo($pdoDB);
        $sqlMode = $version->getSQLMode();
        if (stripos($sqlMode, 'strict') !== false || stripos($sqlMode, 'only_full_group_by') !== false) {
            $this->currentState = 'strict on';
            $this->isOptional   = false;

            return false;
        }
        $this->currentState = 'on';
        $this->isOptional   = true;

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        $description = parent::getDescription();

        if ($this->getResult() === self::RESULT_FAILED) {
            $description .= '<br>Entfernen Sie das define für DB_DEFAULT_SQL_MODE aus Ihrer config.JTL-Shop.ini.php '
                           .'oder setzen Sie dieses explizit auf false, um den SQL-Mode von JTL-Shop korrigieren '
                           .'zu lassen.';
        }

        return $description;
    }
}
