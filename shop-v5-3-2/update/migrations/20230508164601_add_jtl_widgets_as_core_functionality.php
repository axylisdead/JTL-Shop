<?php declare(strict_types=1);
/**
 * Add JTL-Widgets as Core Functionality
 *
 * @author sl
 * @created Mon, 08 May 2023 16:46:01 +0200
 */

use JTL\Plugin\Helper;
use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20230508164601
 */
class Migration_20230508164601 extends Migration implements IMigration
{
    protected $author      = 'sl';
    protected $description = 'Add JTL-Widgets as Core Functionality';

    /**
     * @inheritDoc
     */
    public function up()
    {
        $stmt = "INSERT INTO `tadminwidgets`
        (
            `kPlugin`,
            `cTitle`,
            `cClass`,
            `eContainer`,
            `cDescription`,
            `nPos`,
            `bExpanded`,
            `bActive`
        )
        VALUES
            (0, 'Top10 Suchanfragen', 'Top10Search', 'center', '', 4, 1, 0),
            (0, 'Top10 Bestseller', 'Top10Bestseller', 'center', '', 5, 1, 0),
            (0, 'Shop-Statistiken', 'ShopStats', 'center', ' ', 5, 1, 0),
            (0, 'Letzte Suchanfragen', 'LastSearch', 'center', '', 6, 1, 0),
            (0, 'Letzte Bestellungen', 'LastOrders', 'center', '', 1, 1, 0),
            (0, 'Kampagnen', 'Campaigns', 'center', '', 1, 1, 0),
            (0, 'Anfragen für Freischaltungen', 'UnlockRequestNotifier', 'left', '', 0, 1, 0);
        ";
        $this->getDB()->query($stmt);

        $widgetPlugin = Helper::getPluginById('jtl_widgets');
        if ($widgetPlugin !== null) {
            $oldWidgets = $this->getDB()->getObjects(
                'SELECT cClass, eContainer, nPos, bExpanded, bActive
                    FROM tadminwidgets
                    WHERE kPlugin = :pluginId
                        AND bActive = 1',
                ['pluginId' => $widgetPlugin->getID()]
            );
            foreach ($oldWidgets as $widget) {
                $this->getDB()->update('tadminwidgets', ['kPlugin', 'cClass'], [0, $widget->cClass], (object)[
                    'eContainer' => $widget->eContainer,
                    'nPos'       => $widget->nPos,
                    'bExpanded'  => $widget->bExpanded,
                    'bActive'    => $widget->bActive,
                ]);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        $this->getDB()->queryPrepared("DELETE FROM `tadminwidgets` WHERE `cClass` IN(
                'Top10Search',
                'Top10Bestseller',
                'ShopStats',
                'LastSearch',
                'LastOrders',
                'Campaigns',
                'UnlockRequestNotifier'
            ) AND kPlugin = 0", []);
    }
}
