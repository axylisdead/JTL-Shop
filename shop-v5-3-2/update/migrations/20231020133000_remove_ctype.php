<?php declare(strict_types=1);

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231020133000
 */
class Migration_20231020133000 extends Migration implements IMigration
{
    protected $author = 'fm';
    protected $description = 'Remove cType from teigenschaftwertpict/tkategoriepict';

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('ALTER TABLE `teigenschaftwertpict` DROP COLUMN `cType`');
        $this->execute('ALTER TABLE `tkategoriepict` DROP COLUMN `cType`');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('ALTER TABLE `teigenschaftwertpict` ADD COLUMN `cType` CHAR(1) DEFAULT NULL');
        $this->execute('ALTER TABLE `tkategoriepict` ADD COLUMN `cType` CHAR(1) DEFAULT NULL');
    }
}
