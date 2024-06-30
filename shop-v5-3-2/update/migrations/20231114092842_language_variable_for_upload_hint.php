<?php declare(strict_types=1);
/**
 * Language variable for upload hint
 *
 * @author fp
 * @created Tue, 14 Nov 2023 09:28:42 +0100
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231114092842
 */
class Migration_20231114092842 extends Migration implements IMigration
{
    protected $author = 'fp';
    protected $description = 'Language variable for upload hint';

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function up(): void
    {
        $this->setLocalization('ger', 'checkout', 'missingRecommendedUpload',
            'Sie haben Upload-Artikel in den Warenkorb gelegt, für die Sie Dateien hochladen können, aber noch keine ' .
            'Dateien hinterlegt haben. Bitte überprüfen Sie die Artikel und laden Sie ggf. benötigte Dateien hoch.');
        $this->setLocalization('eng', 'checkout', 'missingRecommendedUpload',
            'You have added items to the shopping basket for which you can upload files. However, you have not ' .
            'uploaded any files yet. Please check the items and upload any necessary files.');
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeLocalization('missingRecommendedUpload', 'checkout');
    }
}
