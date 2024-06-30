<?php declare(strict_types=1);
/**
 * Add column priority to temailvorlage
 *
 * @author sl
 * @created Tue, 09 Jan 2024 13:30:18 +0100
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20240109133018
 */
class Migration_20240109133018 extends Migration implements IMigration
{
    protected $author = 'sl';
    protected $description = 'Add column priority to temailvorlage and emails';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->db->executeQuery('Alter table `temailvorlage` ADD COLUMN `nPrio` TINYINT NULL DEFAULT 100 AFTER `kPlugin`');
        $this->db->executeQuery('Alter table `emails` ADD COLUMN `priority` TINYINT NULL DEFAULT 100 AFTER `customerGroupID`');

        $this->db->executeQuery('UPDATE `temailvorlage` SET nPrio = 0 WHERE `cModulId` = \'core_jtl_passwort_vergessen\'');
        $this->db->executeQuery('UPDATE `temailvorlage` SET nPrio = 1 WHERE `cModulId` = \'core_jtl_bestellbestaetigung\'');

        $this->setConfig(
            'email_send_immediately',
            'Y',
            CONF_EMAILS,
            'Emails direkt versenden',
            'selectbox',
            121,
            (object)[
                'inputOptions' => [
                    'Y' => 'Ja, alle E-Mails sofort versenden',
                    'N' => 'Nein, nur priorisierte E-Mails sofort versenden',
                ]
            ],
        );
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('email_send_immediately');

        $this->db->executeQuery('Alter table temailvorlage DROP COLUMN `nPrio`');
        $this->db->executeQuery('Alter table emails DROP COLUMN `priority`');
    }
}
