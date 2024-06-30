<?php declare(strict_types=1);

namespace Systemcheck\Platform;

use PDO;
use PDOException;
use stdClass;

/**
 * Class PDOConnection
 * @package Systemcheck\Platform
 */
final class PDOConnection
{
    /** @var PDO|null */
    private ?PDO $dbPDO = null;

    /** @var PDOConnection|null */
    private static ?self $instance = null;

    /**
     * PDOConnection constructor
     */
    private function __construct()
    {
        self::$instance = $this;
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        return self::$instance ?? new self();
    }

    /**
     * @param string $host
     * @param string $socket
     * @param string $user
     * @param string $pwd
     * @return stdClass
     */
    public static function createAuth(string $host, string $socket, string $user, string $pwd): stdClass
    {
        return (object)[
            'dbHost'   => $host,
            'dbSocket' => $socket,
            'dbUser'   => $user,
            'dbPwd'    => $pwd,
        ];
    }

    /**
     * @param stdClass $auth
     * @return PDO|null
     */
    private function createConnection(stdClass $auth): ?PDO
    {
        $dsn = 'mysql:';
        if ($auth->dbSocket !== '') {
            $dsn .= 'unix_socket=' . $auth->dbSocket;
        } else {
            $dsn .= 'host=' . $auth->dbHost;
        }

        try {
            $this->dbPDO = new PDO($dsn, $auth->dbUser, $auth->dbPwd);
        } catch (PDOException) {
            return null;
        }

        return $this->dbPDO;
    }

    /**
     * @param stdClass|null $auth
     * @return PDO|null
     */
    public function getConnection(?stdClass $auth = null): ?PDO
    {
        return $this->dbPDO ?? ($auth === null ? null : $this->createConnection($auth));
    }

    /**
     * @param PDO $dbPDO
     * @return $this
     */
    public function setConnection(PDO $dbPDO): self
    {
        $this->dbPDO = $dbPDO;

        return $this;
    }
}
