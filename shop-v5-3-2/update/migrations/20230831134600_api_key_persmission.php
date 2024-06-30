<?php declare(strict_types=1);

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20230831134600
 */
class Migration_20230831134600 extends Migration implements IMigration
{
    protected $author = 'fm';
    protected $description = 'Add API Keys permission';

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute("INSERT INTO `tadminrecht` (`cRecht`, `cBeschreibung`)
            VALUES ('API_KEYS_VIEW', 'API Keys')");
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute("DELETE FROM `tadminrecht` WHERE `cRecht` = 'API_KEYS_VIEW'");
    }
}
