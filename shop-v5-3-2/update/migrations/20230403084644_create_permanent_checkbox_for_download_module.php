<?php declare(strict_types=1);

/**
 * Create permanent checkbox for download module
 *
 * @author sl
 * @created Mon, 03 Apr 2023 08:46:44 +0200
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20230403084644
 */
class Migration_20230403084644 extends Migration implements IMigration
{
    protected $author = 'sl';
    protected $description = 'Create permanent checkbox for download module';

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $customerGroups = $this->getDB()->getSingleObject(
            "SELECT GROUP_CONCAT(kKundengruppe SEPARATOR ';') AS ID
                FROM tkundengruppe"
        );

        $this->execute('ALTER TABLE tcheckbox ADD COLUMN nInternal TINYINT(1) Default 0');
        $result = $this->getDB()->getSingleInt(
            "SELECT 1 AS countNames
                FROM tcheckbox
                WHERE cName = 'RightOfWithdrawalOfDownloadItems'
                LIMIT 1",
            'countNames'
        );
        if ($result <= 0) {
            $kCheckBox = $this->getDB()->insert('tcheckbox', (object)[
                'kLink'             => 0,
                'kCheckBoxFunktion' => 0,
                'cName'             => 'RightOfWithdrawalOfDownloadItems',
                'cKundengruppe'     => ';' . ($customerGroups->ID ?? '-1') . ';',
                'cAnzeigeOrt'       => ';2;',
                'nAktiv'            => 1,
                'nPflicht'          => 1,
                'nLogging'          => 1,
                'nSort'             => 1,
                'dErstellt'         => 'now()',
                'nInternal'         => 1
            ]);

            $cText    = 'Hinweis: Widerrufsrecht erlischt mit Vertragsbeginn.
Ich stimme ausdrücklich zu, dass der Vertrag für digitale Produkte vor Ablauf der Widerrufsfrist beginnt.' .
                ' Mir ist bekannt, dass mit Vertragsbeginn mein Widerrufsrecht erlischt.';
            $cTextEng = 'Please note: Right of withdrawal ends with start of contract.
I hereby acknowledge that the contract for digital products is valid before the end of the withdrawal period.' .
                ' I am aware that my right of withdrawal ends with the start of the contract.';

            $checkboxLang  = [
                [
                    'cISO'          => 'ger',
                    'kCheckBox'     => $kCheckBox,
                    'cText'         => $cText,
                    'cBeschreibung' => ''
                ],
                [
                    'cISO'          => 'eng',
                    'kCheckBox'     => $kCheckBox,
                    'cText'         => $cTextEng,
                    'cBeschreibung' => ''
                ],
            ];
            foreach ($checkboxLang as $lang) {
                $this->getDB()->queryPrepared(
                    'INSERT INTO tcheckboxsprache (kSprache, kCheckBox, cText, cBeschreibung)
                        SELECT tsprache.kSprache, newData.*
                        FROM (
                            SELECT :kCheckBox AS kCheckBox, :cText AS cText, :cBeschreibung AS cBeschreibung
                        ) AS newData
                        INNER JOIN tsprache ON tsprache.cISO = :cISO',
                    $lang
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->execute(
            "DELETE tcheckbox, tcheckboxsprache, tcheckboxlogging
                FROM tcheckbox
                    LEFT JOIN tcheckboxsprache ON tcheckbox.kCheckBox = tcheckboxsprache.kCheckBox
                    LEFT JOIN tcheckboxlogging ON tcheckbox.kCheckBox = tcheckboxlogging.kCheckBox
                WHERE tcheckbox.cName = 'RightOfWithdrawalOfDownloadItems'"
        );
        $this->execute('ALTER TABLE `tcheckbox` DROP COLUMN `nInternal`');
    }
}
