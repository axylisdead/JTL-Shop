<?php declare(strict_types=1);
/**
 * add_config_for_consistent_gross_prices
 *
 * @author dr
 * @created Mon, 20 Nov 2023 15:02:07 +0100
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20231120150207
 */
class Migration_20231120150207 extends Migration implements IMigration
{
    protected $author = 'dr';
    protected $description = 'add_config_for_consistent_gross_prices';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $defaultValue = defined('\CONSISTENT_GROSS_PRICES') && \CONSISTENT_GROSS_PRICES === false ? 'N' : 'Y';
        $this->setConfig(
            'consistent_gross_prices',
            $defaultValue,
            CONF_GLOBAL,
            'Gleichbleibende Bruttopreise',
            'selectbox',
            750,
            (object)[
                'inputOptions' => [
                    'Y' => 'Ja, Bruttopreise unabhängig vom Lieferland',
                    'N' => 'Nein, Bruttopreise abhängig vom Lieferland-Steuersatz',
                ]
            ],
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->removeConfig('consistent_gross_prices');
    }
}
