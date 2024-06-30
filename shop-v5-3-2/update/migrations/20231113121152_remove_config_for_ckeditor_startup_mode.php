<?php declare(strict_types=1);
/**
 * remove config for ckeditor startup mode
 *
 * @author dr
 * @created Mon, 13 Nov 2023 12:11:52 +0100
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231113121152
 */
class Migration_20231113121152 extends Migration implements IMigration
{
    protected $author = 'dr';
    protected $description = 'remove config for ckeditor startup mode';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->removeConfig('admin_ckeditor_mode');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->setConfig(
            'admin_ckeditor_mode',
            'N',
            CONF_GLOBAL,
            'CKEditor-Modus',
            'selectbox',
            1501,
            (object)[
                'inputOptions' => [
                    'Q' => 'Quellcode',
                    'N' => 'Normal',
                ]
            ],
        );
    }
}
