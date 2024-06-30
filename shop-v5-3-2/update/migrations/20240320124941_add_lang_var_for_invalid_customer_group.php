<?php declare
(strict_types=1);

/**
 * add lang var for invalid customer group
 *
 * @author fp
 * @created Wed, 20 Mar 2024 12:49:41 +0100
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20240320124941
 */
class Migration_20240320124941 extends Migration implements IMigration
{
    protected $author = 'fp';
    protected $description = 'add lang var for invalid customer group';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->setLocalization(
            'ger',
            'global',
            'accountInvalidGroup',
            '<b>Sie wurden zur Standardkundengruppe hinzugefügt</b><br>'
            . 'Aufgrund von Unstimmigkeiten bei Ihrer vorherigen Kundengruppe wurden Sie in die Standardkundengruppe '
            . 'verschoben. Bitte setzen Sie sich mit dem Shopbetreiber in Verbindung, um den Grund dafür zu klären. '
            . 'Die Verschiebung kann beispielsweise dazu führen, dass Ihnen andere Preise als zuvor angezeigt werden.');
        $this->setLocalization(
            'eng',
            'global',
            'accountInvalidGroup',
            '<b>You have been added to the default customer group</b><br>'
            . 'Due to irregularities with your previous customer group, you have been moved to the default customer '
            . 'group. Please contact the shop operator to clarify the reason for this. This move may, for example, '
            . 'result in you being shown different prices than before.');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->removeLocalization('accountInvalidGroup', 'global');
    }
}
