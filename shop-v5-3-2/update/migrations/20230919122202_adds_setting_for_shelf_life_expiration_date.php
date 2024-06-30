<?php declare(strict_types=1);
/**
 * adds setting for shelf-life expiration date
 *
 * @author ms
 * @created Tue, 19 Sep 2023 12:22:02 +0200
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20230919122202
 */
class Migration_20230919122202 extends Migration implements IMigration
{
    protected $author = 'ms';
    protected $description = 'adds setting for shelf-life expiration date';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->setConfig(
            'show_shelf_life_expiration_date',
            'Y',
            CONF_ARTIKELDETAILS,
            'Mindesthaltbarkeitsdatum (MHD) anzeigen',
            'selectbox',
            498,
            (object)[
                'cBeschreibung' => 'Hier legen Sie fest, ob Kunden das Mindesthaltbarkeitsdatum von Artikeln im Onlineshop sehen können oder nicht. Dies betrifft alle Stellen, an denen das MHD standardmäßig angezeigt wird.',
                'inputOptions'  => [
                    'N' => 'Nein',
                    'Y' => 'Ja'
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->removeConfig('show_shelf_life_expiration_date');
    }
}
