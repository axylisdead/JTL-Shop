<?php declare(strict_types=1);
/**
 * Delete dependent entries of deleted orders
 *
 * @author fp
 * @created Tue, 08 Aug 2023 15:22:08 +0200
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20230808152208
 */
class Migration_20230808152208 extends Migration implements IMigration
{
    protected $author = 'fp';
    protected $description = 'Delete dependent entries of deleted orders';

    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->execute(
            'DELETE twarenkorb, tgratisgeschenk, twarenkorbpos, twarenkorbposeigenschaft
                FROM twarenkorb
                LEFT JOIN tgratisgeschenk
                    ON tgratisgeschenk.kWarenkorb = twarenkorb.kWarenkorb
                LEFT JOIN twarenkorbpos
                    ON twarenkorbpos.kWarenkorb = twarenkorb.kWarenkorb
                LEFT JOIN twarenkorbposeigenschaft
                    ON twarenkorbposeigenschaft.kWarenkorbPos = twarenkorbpos.kWarenkorbPos
                LEFT JOIN tbestellung
                    ON tbestellung.kWarenkorb = twarenkorb.kWarenkorb
                WHERE tbestellung.kWarenkorb IS NULL'
        );
    }

    /**
     * @inheritDoc
     */
    public function down(): void
    {
        // this migration cannot be rolled back
    }
}
