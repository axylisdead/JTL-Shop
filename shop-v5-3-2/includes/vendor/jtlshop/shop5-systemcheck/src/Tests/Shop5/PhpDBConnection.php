<?php
declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use PDO;
use Systemcheck\Platform\DBServerInfo;
use Systemcheck\Platform\PDOConnection;
use Systemcheck\Tests\ProgramTest;

/**
 * Class PhpDBConnection
 * @package Systemcheck\Tests\Shop5
 */
class PhpDBConnection extends ProgramTest
{
    protected $name          = 'Datenbank-Unterstützung';
    protected $requiredState = 'MySQL, MariaDB';
    protected $description   = 'Für JTL-Shop wird eine MySQL oder MariaDB Datenbank benötigt.';

    /** @var bool */
    private bool $requireAuth = false;

    /**
     * @param PDO|null $pdoDB
     * @return bool
     */
    private function handleAuthenticated(?PDO $pdoDB): bool
    {
        if ($pdoDB === null) {
            $this->requireAuth  = true;
            $this->currentState = 'Anmeldung fehlgeschlagen';

            return false;
        }

        $version            = new DBServerInfo($pdoDB);
        $this->requireAuth  = false;
        $this->currentState = $version->getServer();

        return $version->isSupportedServer();
    }

    /**
     * @return bool
     */
    private function handleNotAuthenticated(): bool
    {
        $this->requireAuth   = true;
        $this->currentState  = 'Login erforderlich';

        return false;
    }

    /**
     * @return bool
     */
    private function handleNotSupported(): bool
    {
        $this->requireAuth   = false;
        $this->currentState  = 'nicht unterstützt';

        return false;
    }

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $pdoTest = new PhpPdoMysqlSupport();
        if ($pdoTest->execute()) {
            $pdoCon = PDOConnection::getInstance();
            $pdoDB  = $pdoCon->getConnection();
            if ($pdoDB !== null) {
                return $this->handleAuthenticated($pdoDB);
            }
            if ((int)($_POST['dbAuth'] ?? 0) === 1) {
                $pdoDB = $pdoCon->getConnection(PDOConnection::createAuth(
                    $_POST['dbHost'] ?? '',
                    $_POST['dbSocket'] ?? '',
                    $_POST['dbUser'] ?? '',
                    $_POST['dbPassword'] ?? ''
                ));

                return $this->handleAuthenticated($pdoDB);
            }

            return $this->handleNotAuthenticated();
        }

        return $this->handleNotSupported();
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): ?string
    {
        $default = parent::getDescription();
        if ($this->requireAuth) {
            $default .= '<form method="post" class="db_check_form">'
                . '<div class="d-flex">'
                . '<input type="hidden" name="dbAuth" value="1" />'
                . '<div class="form-group">'
                . '<label for="dbHost">Datenbank Host</label>'
                . '<input id="dbHost" type="text" class="input-control" name="dbHost" value="localhost" />'
                . '<label for="dbSocket">Socket (optional)</label>'
                . '<input id="dbSocket" type="text" class="input-control" name="dbSocket" />'
                . '</div><div class="form-group">'
                . '<label for="dbUser">Benutzername</label>'
                . '<input id="dbUser" type="text" class="input-control" name="dbUser" />'
                . '<label for="dbPassword">Passwort</label>'
                . '<input id="dbPassword" type="password" class="input-control" name="dbPassword" />'
                . '</div></div>'
                . '<button type="submit" class="btn btn-primary">Testen</button>'
                . '</form>';
        }

        return $default;
    }
}
