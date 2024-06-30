<?php declare(strict_types=1);
/**
 * Add tables and language variables for RMA functionality
 *
 * @author Tim Niko Tegtmeyer
 * @created Fri, 19 May 2023 12:08:34 +0200
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20230519120834
 */
class Migration_20230519120834 extends Migration implements IMigration
{
    protected $author      = 'Tim Niko Tegtmeyer';
    protected $description = 'Add tables and language variables for RMA functionality';

    /**
     * @return stdClass
     */
    private function getLangData(): object
    {
        $newVars      = new stdClass();
        $newVars->rma = [
            'statusArrived' =>
                [
                    'ger' => 'Eingetroffen',
                    'eng' => 'Arrived'
                ]
            , 'statusOpen' =>
                [
                    'ger' => 'Offen',
                    'eng' => 'Open'
                ]
            , 'statusAccepted' =>
                [
                    'ger' => 'Angenommen',
                    'eng' => 'Accepted'
                ]
            , 'statusProcessing' =>
                [
                    'ger' => 'In Bearbeitung',
                    'eng' => 'Processing'
                ]
            , 'statusCompleted' =>
                [
                    'ger' => 'Abgeschlossen',
                    'eng' => 'Completed'
                ]
            , 'statusRejected' =>
                [
                    'ger' => 'Abgelehnt',
                    'eng' => 'Rejected'
                ]
            , 'showItems' =>
                [
                    'ger' => 'Artikel anzeigen',
                    'eng' => 'Show items'
                ]
            , 'createRetoure' =>
                [
                    'ger' => 'Retoure anlegen',
                    'eng' => 'Request RMA'
                ]
            , 'maxAnzahlTitle' =>
                [
                    'ger' => '%s mehr als bestellt.',
                    'eng' => '%s more than ordered.'
                ]
            , 'maxAnzahlText' =>
                [
                    'ger' => 'Sie können nicht mehr Artikel retournieren, als Sie bestellt haben.',
                    'eng' => 'You cannot return more items than you ordered.'
                ]
            , 'noItemsSelectedTitle' =>
                [
                    'ger' => 'Keine Artikel ausgewählt!',
                    'eng' => 'No products selected!'
                ]
            , 'noItemsSelectedText' =>
                [
                    'ger' => 'Sie müssen mindestens einen Artikel zum retournieren auswählen und einen Grund angeben.',
                    'eng' => 'You must select at least one product to return and provide a reason.'
                ]
            , 'noReasonSelectedTitle' =>
                [
                    'ger' => 'Kein Grund angegeben!',
                    'eng' => 'No reason selected!'
                ]
            , 'noReasonSelectedSaveButton' =>
                [
                    'ger' => 'Gründe speichern',
                    'eng' => 'Save reasons'
                ]
            , 'noReasonSelectedText' =>
                [
                    'ger' => 'Sie müssen für jeden Artikel einen Grund angeben.',
                    'eng' => 'You must provide a reason for each product.'
                ]
            , 'noReasonSelectedTextDetailed' =>
                [
                    'ger' => 'Wählen Sie einen Retouren-Grund für alle ausgewählten Produkte welche noch keinen'
                        . ' Rückgabegrund gesetzt haben:',
                    'eng' => 'Select a return reason for all selected products that have not yet set a return reason:'
                ]
            , 'myReturns' =>
                [
                    'ger' => 'Meine Retouren',
                    'eng' => 'My returns'
                ]
            , 'allOrders' =>
                [
                    'ger' => 'Alle Bestellungen',
                    'eng' => 'All orders'
                ]
            , 'addItems' =>
                [
                    'ger' => 'Artikel hinzufügen',
                    'eng' => 'Add items'
                ]
            , 'returnAddress' =>
                [
                    'ger' => 'Adresse',
                    'eng' => 'Address'
                ]
            , 'newReturnAddress' =>
                [
                    'ger' => 'Neue Adresse erstellen',
                    'eng' => 'Create new address'
                ]
            , 'manageReturns' =>
                [
                    'ger' => 'Retouren verwalten',
                    'eng' => 'Manage returns'
                ]
            , 'saveReturn' =>
                [
                    'ger' => 'Retoure speichern',
                    'eng' => 'Save return'
                ]
            , 'addVisibleItems' =>
                [
                    'ger' => 'Sichtbare hinzufügen',
                    'eng' => 'Add visible items'
                ]
            , 'removeVisibleItems' =>
                [
                    'ger' => 'Sichtbare entfernen',
                    'eng' => 'Remove visible items'
                ]
            , 'edit' =>
                [
                    'ger' => 'Retoure ändern',
                    'eng' => 'Modify return'
                ]
            , 'saveRMA' =>
                [
                    'ger' => 'Ihre Retoure wurde erfolgreich gespeichert.',
                    'eng' => 'Your return has been saved successfully.'
                ]
            , 'errorSavingRMA' =>
                [
                    'ger' => 'Ihre Retoure konnte nicht gespeichert werden.',
                    'eng' => 'Your return could not be saved.'
                ]
            , 'partlist' =>
                [
                    'ger' => 'Stückliste',
                    'eng' => 'Part list'
                ]
            , 'rmaLabelNotGenerated' =>
                [
                    'ger' => 'Es wurde noch kein Etikett generiert. Dieses wird automatisch erstellt, sobald die'
                        . ' Retoure vom Shopbetreiber bestätigt wurde.',
                    'eng' => 'No label has been generated yet. This is created automatically as soon as the return has'
                        . ' been confirmed by the shop operator.'
                ]
            , 'rmaSummaryTitle' =>
                [
                    'ger' => 'Retoure Zusammenfassung',
                    'eng' => 'Return summary'
                ]
            , 'rmaSummaryText' =>
                [
                    'ger' => 'Verpacken Sie alle für diese Rücksendung angegebenen Artikel sorgfältig. Die Artikel'
                        . ' müssen bis zum <b>%s</b> an die auf dem Etikett genannte Adresse zurückgeschickt werden.'
                        . ' Wir senden Ihnen diese Zusammenfassung ebenfalls per E-Mail zu, sobald Sie die Retoure'
                        . ' bestätigen.',
                    'eng' => 'Carefully package all items specified for this return. Items must be returned by'
                        . ' <b>%s</b> to the address shown on the label. We will also send you this summary by email as'
                        .' soon as you confirm the return.'
                ]
            , 'rmaSummaryAddressText' =>
                [
                    'ger' => 'Unser Logistikpartner <b>%s</b> holt Ihre Artikel unter folgender Anschrift ab:',
                    'eng' => 'Our logistics partner <b>%s</b> will collect your items from the following address:'
                ]
            , 'rmaDetails' =>
                [
                    'ger' => 'Retoure anzeigen',
                    'eng' => 'Return details'
                ]
            , 'rmaClose' =>
                [
                    'ger' => 'Schließen',
                    'eng' => 'Close'
                ]
            , 'rmaItemsModalTitle' =>
                [
                    'ger' => 'Artikel zur Retoure',
                    'eng' => 'Items for return'
                ]
            , 'rmaID' =>
                [
                    'ger' => 'Retourennummer',
                    'eng' => 'Return ID'
                ]
            , 'rmaSummaryStatusText' =>
                [
                    'ger' => 'Ihre Retoure hat folgenden Status: <b>%s</b><br>Sie haben folgende Anschrift für die'
                        . ' Abholung angegeben:',
                    'eng' => 'Your return has the following status: <b>%s</b><br>You have provided the following'
                        . ' address for collection:'
                ]
            , 'rmaSummaryItemTableTitle' =>
                [
                    'ger' => 'Ihre Retoure umfasst folgende Artikel',
                    'eng' => 'Your return contains the following items'
                ]
            , 'rmaHistoryItemModifiedTitle' =>
                [
                    'ger' => 'Mengenänderung',
                    'eng' => 'Quantity changed'
                ]
            , 'rmaHistoryItemModifiedText' =>
                [
                    'ger' => 'Die zu retournierende Menge für das Produkt <mark>%s</mark>'
                        . ' wurde um <mark>%s</mark>geändert.',
                    'eng' => 'The quantity to be returned for the product <mark>%s</mark>'
                        . ' has been changed by <mark>%s</mark>.'
                ]
            , 'rmaHistoryItemAddedTitle' =>
                [
                    'ger' => 'Produkt hinzugefügt',
                    'eng' => 'Item added'
                ]
            , 'rmaHistoryItemAddedText' =>
                [
                    'ger' => 'Das Produkt <mark>%s</mark> wurde zur Retoure hinzugefügt.',
                    'eng' => 'The product <mark>%s</mark> has been added to the return.'
                ]
            , 'rmaHistoryItemRemovedTitle' =>
                [
                    'ger' => 'Produkt entfernt',
                    'eng' => 'Item removed'
                ]
            , 'rmaHistoryItemRemovedText' =>
                [
                    'ger' => 'Das Produkt <mark>%s</mark> wurde aus der Retoure entfernt.',
                    'eng' => 'The product <mark>%s</mark> has been removed from the return.'
                ]
            , 'rmaHistoryItemModifiedReasonTitle' =>
                [
                    'ger' => 'Rückgabegrund geändert',
                    'eng' => 'Reason for return changed'
                ]
            , 'rmaHistoryItemModifiedReasonText' =>
                [
                    'ger' => 'Der Rückgabegrund für das Produkt <mark>%s</mark> wurde von'
                        . ' <mark>%s</mark> auf <mark>%s</mark> geändert.',
                    'eng' => 'The return reason for the product <mark>%s</mark> has been changed from'
                        . ' <mark>%s</mark> to <mark>%s</mark>.'
                ]
            , 'rmaHistoryReplacementOrderAssignedTitle' =>
                [
                    'ger' => 'Umtausch-Auftrag zugewiesen',
                    'eng' => 'Redemption order assigned'
                ]
            , 'rmaHistoryReplacementOrderAssignedText' =>
                [
                    'ger' => 'Es wurde ein Umtausch-Auftrag mit der Bestellnummer <mark>%s</mark> zugewiesen.',
                    'eng' => 'An exchange order with the order number <mark>%s</mark> has been assigned.'
                ]
            , 'rmaHistoryStatusChangedTitle' =>
                [
                    'ger' => 'Statusänderung',
                    'eng' => 'Change of status'
                ]
            , 'rmaHistoryStatusChangedText' =>
                [
                    'ger' => 'Der Status wurde von <mark>%s</mark> auf <mark>%s</mark> geändert.',
                    'eng' => 'Status changed from <mark>%s</mark> to <mark>%s</mark>.'
                ]
            , 'rmaHistoryAddressModifiedTitle' =>
                [
                    'ger' => 'Adressänderung',
                    'eng' => 'Change of address'
                ]
            , 'rmaHistoryAddressModifiedText' =>
                [
                    'ger' => 'Die hinterlegte Adresse wurde geändert. Vorher sah die Adresse wie folgt aus:<br>'
                        .'<pre class="text-wrap word-break">%s</pre>',
                    'eng' => 'The registered address has been changed. Previously the address looked like this:<br>'
                        .'<pre class="text-wrap word-break">%s</pre>'
                ]
            , 'rmaHistoryRefundShippingTitle' =>
                [
                    'ger' => 'Versandkostenerstattung',
                    'eng' => 'Shipping costs refund'
                ]
            , 'rmaHistoryRefundShippingText' =>
                [
                    'ger' => 'Die Erstattung der Versandkosten wurde <mark>%s</mark>.',
                    'eng' => 'The refund of the shipping costs has been <mark>%s</mark>.'
                ]
            , 'rmaHistoryVoucherCreditTitle' =>
                [
                    'ger' => 'Gutschein und Rabatte',
                    'eng' => 'Voucher and discounts'
                ]
            , 'rmaHistoryVoucherCreditText' =>
                [
                    'ger' => 'Die Erstattung von Gutscheinen und Rabatten wurde <mark>%s</mark>.',
                    'eng' => 'The refund of vouchers and discounts has been <mark>%s</mark>.'
                ]
            , 'rmaHistoryRefundAccepted' =>
                [
                    'ger' => 'akzeptiert',
                    'eng' => 'accepted'
                ]
            , 'rmaHistoryRefundDenied' =>
                [
                    'ger' => 'abgelehnt',
                    'eng' => 'denied'
                ]
            , 'youHaveNoShippingAddress' =>
                [
                    'ger' => 'Sie haben noch keine Lieferadressen.',
                    'eng' => 'You have no shipping addresses yet.'
                ]
            , 'rmaNoItems' =>
                [
                    'ger' => 'Sie haben noch keine Warenrücksendungen.',
                    'eng' => 'No returns items yet.'
                ]
            , 'rmaChangelog' =>
                [
                    'ger' => 'Changelog',
                    'eng' => 'Changelog'
                ]
            , 'rmaReason' =>
                [
                    'ger' => 'Rückgabegrund',
                    'eng' => 'Returns reason'
                ]
            , 'rmaQuantity' =>
                [
                    'ger' => 'Anzahl',
                    'eng' => 'Quantity'
                ]
            , 'rmaImage' =>
                [
                    'ger' => 'Bild',
                    'eng' => 'Image'
                ]
            , 'rmaName' =>
                [
                    'ger' => 'Name',
                    'eng' => 'Name'
                ]
        ];

        $newVars->datatables = [
            'search' =>
                [
                    'ger' => 'Suche',
                    'eng' => 'Search'
                ]
            , 'lengthMenu' =>
                [
                    'ger' => '_MENU_ Einträge anzeigen',
                    'eng' => 'Show _MENU_ entries'
                ]
        ];

        return $newVars;
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        // Add new config
        $this->setConfig(
            configName: 'global_rma_enabled',
            configValue: 'N',
            configSection: CONF_GLOBAL,
            externalName: 'Retourenmanagement aktivieren',
            inputType: 'selectbox',
            sort: 651,
            additionalProperties: (object)[
                'cBeschreibung' => 'Möchten Sie das Retourenmanagement im Shop aktivieren?',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ],
            overwrite: true
        );
        $this->execute(
            "CREATE TABLE `rma` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `wawiID` INT UNSIGNED DEFAULT NULL,
                `customerID` INT UNSIGNED NOT NULL DEFAULT 0,
                `replacementOrderID` INT UNSIGNED DEFAULT NULL,
                `rmaNr` VARCHAR(20) DEFAULT NULL,
                `voucherCredit` TINYINT NOT NULL DEFAULT 0,
                `refundShipping` TINYINT NOT NULL DEFAULT 0,
                `synced` TINYINT NOT NULL DEFAULT 0,
                `status` TINYINT NOT NULL DEFAULT 1,
                `comment` MEDIUMTEXT DEFAULT NULL,
                `createDate` DATETIME NOT NULL,
                `lastModified` DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
                PRIMARY KEY (`id`),
                UNIQUE INDEX idx_rma_wawiID (`wawiID`),
                INDEX idx_rma_customerID (`customerID`)
            )
            COMMENT='Store return requests created in shop or imported from WaWi.'
            DEFAULT CHARSET=utf8mb4
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB"
        );

        $this->execute(
            "CREATE TABLE `rma_items` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `rmaID` INT UNSIGNED NOT NULL,
                `shippingNotePosID` INT UNSIGNED DEFAULT NULL,
                `orderID` INT UNSIGNED DEFAULT NULL,
                `orderPosID` INT UNSIGNED DEFAULT NULL,
                `productID` INT UNSIGNED DEFAULT NULL,
                `reasonID` INT UNSIGNED DEFAULT NULL,
                `name` VARCHAR(255) NOT NULL DEFAULT '',
                `variationProductID` INT UNSIGNED DEFAULT NULL,
                `variationName` VARCHAR(255) DEFAULT NULL,
                `variationValue` VARCHAR(255) DEFAULT NULL,
                `partListProductID` INT UNSIGNED DEFAULT NULL,
                `partListProductName` VARCHAR(255) DEFAULT NULL,
                `partListProductURL` VARCHAR(255) DEFAULT NULL,
                `partListProductNo` VARCHAR(255) DEFAULT NULL,
                `unitPriceNet` DOUBLE NOT NULL DEFAULT 0,
                `quantity` DOUBLE NOT NULL DEFAULT 0,
                `vat` FLOAT NOT NULL DEFAULT 0.00,
                `unit` VARCHAR(255) DEFAULT NULL,
                `comment` MEDIUMTEXT DEFAULT NULL,
                `status` CHAR(2) DEFAULT NULL,
                `createDate` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                INDEX idx_rma_items_rmaID (`rmaID`),
                INDEX idx_rma_items_shippingNotePosID (`shippingNotePosID`),
                INDEX idx_rma_items_orderID (`orderID`),
                INDEX idx_rma_items_productID (`productID`),
                INDEX idx_rma_items_reasonID (`reasonID`),
                INDEX idx_rma_items_status (`status`),
                CONSTRAINT `fk_rma_pos_rmaID`
                    FOREIGN KEY (`rmaID`)
                        REFERENCES `rma`(`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
            )
            COMMENT='Store items for RMA requests.'
            DEFAULT CHARSET=utf8mb4
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB"
        );
        
        $this->execute(
            "CREATE TABLE `rma_history` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `rmaID` INT UNSIGNED NOT NULL,
                `eventName` VARCHAR(40) NOT NULL,
                `eventDataJson` MEDIUMTEXT NOT NULL,
                `createDate` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                INDEX idx_rma_history_rmaID (`rmaID`),
                INDEX idx_rma_history_eventName (`eventName`),
                CONSTRAINT `fk_rma_history_rmaID`
                    FOREIGN KEY (`rmaID`)
                        REFERENCES `rma`(`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
            )
            COMMENT='Log RMA modifications.'
            DEFAULT CHARSET=utf8mb4
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB"
        );
        
        $this->execute(
            "CREATE TABLE `return_address` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `rmaID` INT UNSIGNED NOT NULL,
                `customerID` INT UNSIGNED NOT NULL,
                `salutation` VARCHAR(20) NOT NULL,
                `firstName` VARCHAR(255) NOT NULL,
                `lastName` VARCHAR(255) NOT NULL,
                `academicTitle` VARCHAR(64) DEFAULT NULL,
                `companyName` VARCHAR(255) DEFAULT NULL,
                `companyAdditional` VARCHAR(255) DEFAULT NULL,
                `street` VARCHAR(255) NOT NULL,
                `houseNumber` VARCHAR(32) NOT NULL,
                `addressAdditional` VARCHAR(255) DEFAULT NULL,
                `postalCode` VARCHAR(20) NOT NULL,
                `city` VARCHAR(255) NOT NULL,
                `state` VARCHAR(255) DEFAULT NULL,
                `countryISO` VARCHAR(2) NOT NULL,
                `phone` VARCHAR(255) DEFAULT NULL,
                `mobilePhone` VARCHAR(255) DEFAULT NULL,
                `fax` VARCHAR(255) DEFAULT NULL,
                `mail` VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX idx_return_address_customerID (`customerID`),
                UNIQUE INDEX idx_return_address_rmaID (`rmaID`),
                CONSTRAINT `fk_return_address_rmaID`
                    FOREIGN KEY (`rmaID`)
                        REFERENCES `rma`(`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
            )
            COMMENT='Client address for picking up the RMA products.'
            DEFAULT CHARSET=utf8mb4
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB"
        );
        
        $this->execute(
            "CREATE TABLE `rma_reasons` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `wawiID` INT UNSIGNED NOT NULL,
                `productTypeGroupID` INT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX idx_rma_reasons_wawiID (`wawiID`)
            )
            COMMENT='Possible RMA reasons synced from WaWi.'
            DEFAULT CHARSET=utf8mb4
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB"
        );
        
        $this->execute(
            "CREATE TABLE `rma_reasons_lang` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `reasonID` INT UNSIGNED NOT NULL,
                `langID` INT UNSIGNED NOT NULL,
                `title` VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                INDEX idx_rma_reasons_lang_reasonID (`reasonID`),
                INDEX idx_rma_reasons_lang_langID (`langID`),
                CONSTRAINT `fk_rma_reasons_lang_reasonID`
                    FOREIGN KEY (`reasonID`)
                        REFERENCES `rma_reasons`(`id`)
                        ON DELETE CASCADE
                        ON UPDATE CASCADE
            )
            COMMENT='Localized RMA reasons.'
            DEFAULT CHARSET=utf8mb4
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB"
        );
        // Insert default RMA reasons
        $germanLangID = $this->getDB()->getSingleInt(
            'SELECT kSprache
                FROM tsprache
                WHERE cISO = :cISO',
            'kSprache',
            ['cISO'  => 'ger',]
        );
        if ($germanLangID > 0) {
            $rmaReasons = [
                1 => 'Keine Angabe',
                13 => 'Passt nicht',
                14 => 'Gefällt nicht',
                15 => 'Entsprach nicht Beschreibung',
                16 => 'Mehrfach bestellt',
                17 => 'Zu lange Lieferzeit',
                18 => 'Falsche Bestellung',
                19 => 'Woanders günstiger',
                20 => 'Rückrufaktion',
                21 => 'Defekt / beschädigt'
            ];
            foreach ($rmaReasons as $wawiID => $title) {
                $reasonID = $this->getDB()->insert('rma_reasons', (object)[
                    'wawiID'             => $wawiID,
                    'productTypeGroupID' => '_DBNULL_'
                ]);
                $this->getDB()->insert('rma_reasons_lang', (object)[
                    'reasonID' => $reasonID,
                    'langID'   => $germanLangID,
                    'title'    => $title
                ]);
            }
        }

        // Remove old RMA tables
        $this->execute('DROP TABLE IF EXISTS trma');
        $this->execute('DROP TABLE IF EXISTS trmaartikel');
        $this->execute('DROP TABLE IF EXISTS trmagrund');
        $this->execute('DROP TABLE IF EXISTS trmastatus');

        foreach ($this->getLangData() as $sprachsektion => $arr) {
            foreach ($arr as $key => $values) {
                foreach ($values as $iso => $value) {
                    $this->setLocalization($iso, $sprachsektion, $key, $value);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down(): void
    {
        $this->removeConfig('global_rma_enabled');
        $this->execute('SET foreign_key_checks = 0');
        $this->execute('DROP TABLE IF EXISTS return_address');
        $this->execute('DROP TABLE IF EXISTS rma_history');
        $this->execute('DROP TABLE IF EXISTS rma_items');
        $this->execute('DROP TABLE IF EXISTS rma_reasons_lang');
        $this->execute('DROP TABLE IF EXISTS rma_reasons');
        $this->execute('DROP TABLE IF EXISTS rma');
        $this->execute('SET foreign_key_checks = 1');

        foreach ($this->getLangData() as $sprachsektion => $arr) {
            foreach ($arr as $key => $values) {
                $this->removeLocalization($key, $sprachsektion);
            }
        }
        
        // These language variables already exists from a previous migration and need to be overwritten
        $newVars = [
            'search' =>
                [
                    'ger' => 'Adresssuche',
                    'eng' => 'Search address'
                ]
            , 'lengthMenu' =>
                [
                    'ger' => '_MENU_ Adressen anzeigen',
                    'eng' => 'Show _MENU_ addresses'
                ]
        ];
        foreach ($newVars as $key => $values) {
            foreach ($values as $iso => $value) {
                $this->setLocalization($iso, 'datatables', $key, $value);
            }
        }
    }
}
