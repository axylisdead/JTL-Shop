<?php declare(strict_types=1);
/**
 * Add OPC custom input table
 *
 * @author dr
 * @created Fri, 13 Oct 2023 15:02:46 +0200
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231013150246
 */
class Migration_20231013150246 extends Migration implements IMigration
{
    protected $author = 'dr';
    protected $description = 'Add OPC custom input table';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute('CREATE TABLE portlet_input_type (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            plugin_id INT UNSIGNED NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY name_index (name)
        ) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute('DROP TABLE portlet_input_type');
    }
}
