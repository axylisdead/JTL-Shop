<?php declare(strict_types=1);

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20230220143900
 */
class Migration_20230220143900 extends Migration implements IMigration
{
    protected $author = 'fm';
    protected $description = 'Create API key table';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute(
            'CREATE TABLE IF NOT EXISTS `api_keys` (
                `id`           INT          NOT NULL AUTO_INCREMENT,
                `key`          VARCHAR(255) NOT NULL,
                `permissions`  INT          NOT NULL DEFAULT 0,
                `created`      DATETIME     NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS `api_keys`');
    }
}
