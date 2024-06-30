<?php declare(strict_types=1);

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231016122200
 */
class Migration_20231016122200 extends Migration implements IMigration
{
    protected $author = 'fm';
    protected $description = 'Remove APC caching method - reverted 2024-01-08';

    /**
     * @inheritdoc
     */
    public function up(): void
    {
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
    }
}
