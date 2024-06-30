<?php declare(strict_types=1);

namespace Systemcheck\Tests;

use PDO;
use Systemcheck\Platform\PDOConnection;
use Systemcheck\Tests\Shop5\PhpDBConnection;

/**
 * Class DBConnectionTest
 * @package Systemcheck\Tests
 */
abstract class DBConnectionTest extends ProgramTest
{
    /** @var PDO|null */
    private ?PDO $pdoDB = null;

    protected $isRecommended = true;

    /**
     * @return PDO|null
     */
    protected function getPdoDB(): ?PDO
    {
        return $this->pdoDB ?? ($this->pdoDB = PDOConnection::getInstance()->getConnection());
    }

    /**
     * @return bool
     */
    protected function handleNotSupported(): bool
    {
        $this->isOptional    = true;
        $this->isRecommended = false;
        $this->currentState  = 'nicht unterstützt';

        return false;
    }

    /**
     * @return bool
     */
    abstract protected function handleDBAvailable(): bool;

    /**
     * @inheritDoc
     */
    public function execute(): bool
    {
        $dbTest = new PhpDBConnection();
        if ($dbTest->execute()) {
            return $this->handleDBAvailable();
        }

        return $this->handleNotSupported();
    }
}
