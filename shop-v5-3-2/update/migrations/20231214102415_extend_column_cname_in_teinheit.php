<?php declare(strict_types=1);
/**
 * Extend column cName in teinheit
 *
 * @author sl
 * @created Thu, 14 Dec 2023 10:24:15 +0100
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231214102415
 */
class Migration_20231214102415 extends Migration implements IMigration
{
    protected $author      = 'sl';
    protected $description = 'Extend column cName in teinheit';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute('ALTER TABLE `teinheit` CHANGE COLUMN `cName` `cName` VARCHAR(255)');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute('ALTER TABLE `teinheit` CHANGE COLUMN `cName` `cName` VARCHAR(20)');
    }
}
