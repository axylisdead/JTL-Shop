<?php declare(strict_types=1);

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20240108134800
 */
class Migration_20240108134800 extends Migration implements IMigration
{
    protected $author = 'fm';
    protected $description = 'Re-add apc caching method';

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $exists = $this->db->getSingleObject(
            'SELECT *
                FROM teinstellungenconfwerte
                WHERE kEinstellungenConf = 1551
                    AND cName = \'APC\''
        );
        if ($exists === null) {
            $this->execute("
                INSERT INTO teinstellungenconfwerte 
                (`kEinstellungenConf`, `cName`, `cWert`, `nSort`)
                VALUES ('1551','APC','apc','3')"
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('DELETE FROM teinstellungenconfwerte
            WHERE kEinstellungenConf = 1551
                AND cName = \'APC\''
        );
    }
}
