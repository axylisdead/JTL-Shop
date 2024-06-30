<?php declare(strict_types=1);

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231011105200
 */
class Migration_20231011105200 extends Migration implements IMigration
{
    protected $author = 'fm';
    protected $description = 'Drop table tkategoriemapping';

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->execute('DROP TABLE tkategoriemapping');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute('CREATE TABLE `tkategoriemapping` (
          `kKategorie` int(10) unsigned NOT NULL,
          `cName` varchar(255) NOT NULL,
          PRIMARY KEY (`kKategorie`,`cName`)
        ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }
}
