<?php declare(strict_types=1);

use JTL\Helpers\Text;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231026154000
 */
class Migration_20231026154000 extends Migration implements IMigration
{
    protected $author = 'fm';
    protected $description = 'Image size selection for branding';

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $default = Text::createSSK(['xl', 'lg']);
        $this->execute(
            'ALTER TABLE `tbrandingeinstellung` 
                ADD COLUMN `imagesizes` VARCHAR(255) NOT NULL DEFAULT \'' . $default . '\''
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            'ALTER TABLE `tbrandingeinstellung` 
                DROP COLUMN `imagesizes`'
        );
    }
}
