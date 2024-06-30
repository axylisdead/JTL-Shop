<?php declare(strict_types=1);

namespace Systemcheck\Platform;

use Exception;
use PDO;

/**
 * Class DBServerInfo
 * @package JTL\DB
 */
final class DBServerInfo
{
    public const SERVER_MYSQL   = 'MySQL';
    public const SERVER_MARIADB = 'MariaDB';
    public const SERVER_UNKNOWN = 'unbekannt';

    public const NOT_SUPPORTED = 0;
    public const MIN_SUPPORTED = 1;
    public const SUPPORTED     = 2;
    public const MAX_SUPPORTED = 3;

    private const MIN_VERSION             = [
        self::SERVER_MYSQL   => '5.7.0',
        self::SERVER_MARIADB => '10.1.0'
    ];
    private const MIN_RECOMMENDED_VERSION = [
        self::SERVER_MYSQL   => '8.0.0',
        self::SERVER_MARIADB => '10.5.0'
    ];
    private const MAX_VERSION             = [
        self::SERVER_MYSQL   => null,
        self::SERVER_MARIADB => '10.12.0'
    ];

    /** @var PDO */
    private PDO $pdoDb;

    /** @var string|null */
    private ?string $serverInfo = null;

    /** @var string|null */
    private ?string $serverVersion = null;

    /** @var string|null */
    private ?string $server = null;

    /** @var bool|null */
    private ?bool $innoDBSupport = null;

    /** @var bool|null */
    private ?bool $utf8Support = null;

    /** @var int|null */
    private ?int $innoDBSize = null;

    /** @var string|null */
    private ?string $sqlMode = null;

    /**
     * DBServerInfo constructor
     * @param PDO $pdoDb
     */
    public function __construct(PDO $pdoDb)
    {
        $this->pdoDb = $pdoDb;
    }

    /**
     * @return string
     */
    private function getServerInfo(): string
    {
        return $this->serverInfo ?? ($this->serverInfo = $this->pdoDb->getAttribute(PDO::ATTR_SERVER_VERSION));
    }

    /**
     * @param string $info
     * @return string
     */
    private function splitServerInfo(string $info = 'server'): string
    {
        $serverInfo = $this->getServerInfo();
        if (preg_match('/([\d.]+)-?([\da-zA-Z]+)?/', $serverInfo, $hits)) {
            $this->server        = stripos($hits[2] ?? '', 'maria') !== false
                ? self::SERVER_MARIADB
                : self::SERVER_MYSQL;
            $this->serverVersion = $hits[1];
        } else {
            $this->server        = self::SERVER_UNKNOWN;
            $this->serverVersion = '';
        }

        return $info === 'server' ? $this->server : $this->serverVersion;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->serverVersion ?? $this->splitServerInfo('version');
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server ?? $this->splitServerInfo();
    }

    /**
     * @return bool
     */
    public function isSupportedServer(): bool
    {
        return \in_array($this->getServer(), [self::SERVER_MYSQL, self::SERVER_MARIADB]);
    }

    /**
     * @return int
     */
    public function isSupportedVersion(): int
    {
        $version    = $this->getVersion();
        $maxVersion = $this->getMaxSupportedVersion();
        if ($maxVersion !== null && \version_compare($version, $maxVersion, '>=')) {
            return self::MAX_SUPPORTED;
        }

        if (\version_compare($version, $this->getMinSupportedVersion(), '>=')
            && \version_compare($version, $this->getRecommendedVersion(), '<')
        ) {
            return self::MIN_SUPPORTED;
        }

        if (($maxVersion === null || \version_compare($version, $maxVersion, '<'))
            && \version_compare($version, $this->getRecommendedVersion(), '>=')
        ) {
            return self::SUPPORTED;
        }

        return self::NOT_SUPPORTED;
    }

    /**
     * @return bool
     */
    public function hasInnoDBSupport(): bool
    {
        if ($this->innoDBSupport !== null) {
            return $this->innoDBSupport;
        }

        try {
            $res = $this->pdoDb->prepare(
                /** @lang text */
                "SELECT `SUPPORT`
                    FROM information_schema.ENGINES
                    WHERE `ENGINE` = 'InnoDB'"
            );
            if ($res->execute() === false || !($support = $res->fetchObject())) {
                return false;
            }

            $this->innoDBSupport = \in_array($support->SUPPORT ?? 'NONE', ['YES', 'DEFAULT'], true);
            if ($this->innoDBSupport === false || $this->getServer() !== self::SERVER_MYSQL) {
                return $this->innoDBSupport;
            }

            $this->innoDBSupport = null;

            $res = $this->pdoDb->prepare(
                "SHOW VARIABLES LIKE 'innodb_version'"
            );
            if ($res->execute() === false || !($version = $res->fetchObject())) {
                return false;
            }

            $this->innoDBSupport = \version_compare($version->Value, '5.6.0', '>=');
        } catch (Exception) {
            return false;
        }

        return $this->innoDBSupport;
    }

    /**
     * @return bool
     */
    public function hasUTF8Support(): bool
    {
        if ($this->utf8Support !== null) {
            return $this->utf8Support;
        }

        try {
            $res = $this->pdoDb->prepare(
                /** @lang text */
                "SELECT `IS_COMPILED`
                    FROM information_schema.COLLATIONS
                    WHERE `COLLATION_NAME` RLIKE 'utf8(mb4)?_unicode_ci'"
            );
            if ($res->execute() === false || !($utf8 = $res->fetchObject())) {
                return false;
            }

            $this->utf8Support = \strcasecmp($utf8->IS_COMPILED ?? 'no', 'yes') === 0;
        } catch (Exception) {
            return false;
        }

        return $this->utf8Support;
    }

    /**
     * @param string|null $server
     * @return string
     */
    public function getMinSupportedVersion(?string $server = null): string
    {
        return self::MIN_VERSION[$server ?? $this->getServer()] ?? '';
    }

    /**
     * @param string|null $server
     * @return string
     */
    public function getRecommendedVersion(?string $server = null): string
    {
        return self::MIN_RECOMMENDED_VERSION[$server ?? $this->getServer()] ?? '';
    }

    /**
     * @param string|null $server
     * @return string|null
     */
    public function getMaxSupportedVersion(?string $server = null): ?string
    {
        return self::MAX_VERSION[$server ?? $this->getServer()] ?? null;
    }

    /**
     * @return int
     */
    public function getInnoDBSize(): int
    {
        if ($this->innoDBSize !== null) {
            return $this->innoDBSize;
        }

        if (!$this->hasInnoDBSupport()) {
            $this->innoDBSize = -1;

            return $this->innoDBSize;
        }

        $res = $this->pdoDb->prepare('SELECT @@innodb_data_file_path AS path');
        if ($res->execute() === false || !($innodbPath = $res->fetchObject())) {
            return -1;
        }

        $this->innoDBSize = 0;
        if (\stripos($innodbPath->path, 'autoextend') === false) {
            $paths = \explode(';', $innodbPath->path);
            foreach ($paths as $path) {
                if (\preg_match('/:(\d+)([MGTKmgtk]+)/', $path, $hits)) {
                    $this->innoDBSize += match (\strtoupper($hits[2])) {
                        'T'     => (int)$hits[1] * 1024 * 1024 * 1024 * 1024,
                        'G'     => (int)$hits[1] * 1024 * 1024 * 1024,
                        'M'     => (int)$hits[1] * 1024 * 1024,
                        'K'     => (int)$hits[1] * 1024,
                        default => (int)$hits[1],
                    };
                }
            }
        }

        return $this->innoDBSize;
    }

    /**
     * @return string|null
     */
    public function getSQLMode(): ?string
    {
        if ($this->sqlMode !== null) {
            return $this->sqlMode;
        }

        $res = $this->pdoDb->prepare(
            "SHOW VARIABLES LIKE 'sql_mode'"
        );
        if ($res->execute() === false || !($sqlMode = $res->fetchObject())) {
            return null;
        }

        return $sqlMode->Value;
    }
}
