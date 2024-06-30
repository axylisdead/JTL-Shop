<?php declare(strict_types=1);
/**
 * modify rma language variables
 *
 * @author tnt
 * @created Thu, 04 Jan 2024 11:50:50 +0100
 */

use JTL\Update\IMigration;
use JTL\Update\Migration;

/**
 * Class Migration_20240104115050
 */
class Migration_20240104115050 extends Migration implements IMigration
{
    protected $author = 'tnt';
    protected $description = 'modify rma language variables';

    /**
     * @return array
     */
    private function getLangData(): array
    {
        return [
            'statusArrived' =>
                [
                    'ger' => 'Eingetroffen',
                    'eng' => 'Arrived'
                ]
            , 'statusArrivedTooltip' =>
                [
                    'ger' => 'Eines oder mehrere Ihrer zurückgesendeten Pakete sind beim Händler eingetroffen.',
                    'eng' => 'One or more of your returned packages have arrived at the merchant\'s address.'
                ]
            , 'statusOpen' =>
                [
                    'ger' => 'Offen',
                    'eng' => 'Open'
                ]
            , 'statusOpenTooltip' =>
                [
                    'ger' => 'Der Händler hat Ihre Retourenanfrage noch nicht bearbeitet.',
                    'eng' => 'The merchant has not yet processed your return request.'
                ]
            , 'statusAccepted' =>
                [
                    'ger' => 'Angenommen',
                    'eng' => 'Accepted'
                ]
            , 'statusAcceptedTooltip' =>
                [
                    'ger' => 'Der Händler hat Ihre Retourenanfrage akzeptiert. Sie können die Artikel zurücksenden.',
                    'eng' => 'The merchant has accepted your return request. You can send back the items.'
                ]
            , 'statusProcessing' =>
                [
                    'ger' => 'In Bearbeitung',
                    'eng' => 'In progress'
                ]
            , 'statusProcessingTooltip' =>
                [
                    'ger' => 'Es sind alle zurückgesendeten Artikel beim Händler eingegangen und werden nun geprüft.',
                    'eng' => 'All returned items have arrived at the merchant\'s address and are being checked.'
                ]
            , 'statusCompleted' =>
                [
                    'ger' => 'Abgeschlossen',
                    'eng' => 'Completed'
                ]
            , 'statusCompletedTooltip' =>
                [
                    'ger' => 'Der Händler hat Ihre zurückgesendeten Artikel geprüft und einem Umtausch oder einer'
                        . ' Erstattung zugestimmt.',
                    'eng' => 'The merchant has checked the returned items and has agreed to replace or refund the items.'
                ]
            , 'statusRejected' =>
                [
                    'ger' => 'Abgewiesen',
                    'eng' => 'Rejected'
                ]
            , 'statusRejectedTooltip' =>
                [
                    'ger' => 'Der Händler hat Ihre Retourenanfrage abgelehnt. Eine Rücksendung ist nicht möglich.',
                    'eng' => 'The merchant has declined your return request. Returning the items is not possible.'
                ]
            , 'statusClarify' =>
                [
                    'ger' => 'Klärungsbedarf',
                    'eng' => 'Need for clarification'
                ]
            , 'statusClarifyTooltip' =>
                [
                    'ger' => 'Der Händler hat die zurückgesendeten Artikel geprüft und Unregelmäßigkeiten bei Ihrer'
                        . ' Retoure festgestellt. Bitte klären Sie den Fall mit dem Händler.',
                    'eng' => 'The merchant has checked the returned items and has found irregularities regarding the'
                        . ' return. Please resolve the issue with the merchant.'
                ]
            , 'showItems' =>
                [
                    'ger' => 'Artikel anzeigen',
                    'eng' => 'View items'
                ]
            , 'createRetoure' =>
                [
                    'ger' => 'Retourenanfrage erstellen',
                    'eng' => 'Create return request'
                ]
            , 'maxAnzahlTitle' =>
                [
                    'ger' => '%s mehr als bestellt',
                    'eng' => '%s more than ordered'
                ]
            , 'maxAnzahlText' =>
                [
                    'ger' => 'Sie haben %s mehr für die Retoure ausgewählt als ursprünglich bestellt.',
                    'eng' => 'You have selected %s more to be returned than you ordered originally.'
                ]
            , 'noItemsSelectedTitle' =>
                [
                    'ger' => 'Keine Artikel ausgewählt!',
                    'eng' => 'No items selected!'
                ]
            , 'noItemsSelectedText' =>
                [
                    'ger' => 'Bitte wählen Sie mindestens einen Artikel aus, den Sie zurückgeben möchten, und geben Sie'
                        . ' einen Grund für die Retoure an.',
                    'eng' => 'Please select at least one item you wish to return and enter a return reason.'
                ]
            , 'noReasonSelectedTitle' =>
                [
                    'ger' => 'Bitte geben Sie den Grund für die Retoure an.',
                    'eng' => 'Please enter a return reason.'
                ]
            , 'noReasonSelectedSaveButton' =>
                [
                    'ger' => 'Gründe speichern',
                    'eng' => 'Save reasons'
                ]
            , 'noReasonSelectedText' =>
                [
                    'ger' => 'Bitte geben Sie für jede Retoure einen Grund an!',
                    'eng' => 'Please enter a return reason for every item to be returned!'
                ]
            , 'noReasonSelectedTextDetailed' =>
                [
                    'ger' => 'Wählen Sie einen Rückgabegrund für alle ausgewählten Artikel, für die noch kein'
                        . ' Rückgabegrund eingegeben wurde:',
                    'eng' => 'Select a return reason for all selected items for which no return reason has been'
                        . ' entered yet:'
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
                    'eng' => 'Add item'
                ]
            , 'returnAddress' =>
                [
                    'ger' => 'Adresse',
                    'eng' => 'Address'
                ]
            , 'newReturnAddress' =>
                [
                    'ger' => 'Neue Adresse anlegen',
                    'eng' => 'Create new address'
                ]
            , 'manageReturns' =>
                [
                    'ger' => 'Retouren verwalten',
                    'eng' => 'Manage returns'
                ]
            , 'saveReturn' =>
                [
                    'ger' => 'Retoure anlegen',
                    'eng' => 'Create return'
                ]
            , 'addVisibleItems' =>
                [
                    'ger' => 'Sichtbare hinzufügen',
                    'eng' => 'Add all visible '
                ]
            , 'removeVisibleItems' =>
                [
                    'ger' => 'Sichtbare entfernen',
                    'eng' => 'Remove all visible'
                ]
            , 'edit' =>
                [
                    'ger' => 'Retoure bearbeiten',
                    'eng' => 'Edit return'
                ]
            , 'saveRMA' =>
                [
                    'ger' => 'Ihre Retoure wurde erfolgreich angelegt.',
                    'eng' => 'Your return has been successfully created.'
                ]
            , 'errorSavingRMA' =>
                [
                    'ger' => 'Ihre Retoure konnte nicht angelegt werden.',
                    'eng' => 'Your return could not be created.'
                ]
            , 'partlist' =>
                [
                    'ger' => 'Stückliste',
                    'eng' => 'Bill of materials'
                ]
            , 'rmaLabelNotGenerated' =>
                [
                    'ger' => 'Es wurde noch kein Rücksendeetikett generiert. Das Etikett wird automatisch erstellt,'
                        . ' sobald der Händler Ihre Retourenanfrage angenommen hat.',
                    'eng' => 'No return label has been generated yet. The label will be created automatically once the'
                        . ' merchant has accepted your return request.'
                ]
            , 'rmaSummaryTitle' =>
                [
                    'ger' => 'Zusammenfassung der Retoure',
                    'eng' => 'Return summary'
                ]
            , 'rmaSummaryText' =>
                [
                    'ger' => 'Verpacken Sie alle für diese Rücksendung angegebenen Artikel sorgfältig. Die Artikel'
                        . ' müssen bis zum <b>%s</b> an die auf dem Rücksendeetikett genannte Adresse geschickt werden.'
                        . ' Wir senden Ihnen diese Zusammenfassung ebenfalls per E-Mail zu.',
                    'eng' => 'Make sure to carefully pack all items that are part of this return. The items need to be'
                        . ' sent to the address on the return label until <b>%s</b>. We will also send you a summary'
                        . ' via email.'
                ]
            , 'rmaSummaryAddressText' =>
                [
                    'ger' => 'Der Logistikpartner <b>%s</b> holt Ihre Artikel unter folgender Anschrift ab:',
                    'eng' => 'The shipping service provider <b>%s</b> will pick up your items at the following address:'
                ]
            , 'rmaDetails' =>
                [
                    'ger' => 'Retoure anzeigen',
                    'eng' => 'View return'
                ]
            , 'rmaClose' =>
                [
                    'ger' => 'Schließen',
                    'eng' => 'Close'
                ]
            , 'rmaItemsModalTitle' =>
                [
                    'ger' => 'In dieser Retoure enthaltene Artikel',
                    'eng' => 'Items included in this return'
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
                    'eng' => 'Your return has the following status: <b>%s</b><br>You have entered the following address'
                        . ' for pickup.:'
                ]
            , 'rmaSummaryItemTableTitle' =>
                [
                    'ger' => 'Ihre Retoure umfasst folgende Artikel',
                    'eng' => 'Your return includes the following items'
                ]
            , 'rmaHistoryItemModifiedTitle' =>
                [
                    'ger' => 'Mengenänderung',
                    'eng' => 'Change quantity'
                ]
            , 'rmaHistoryItemModifiedText' =>
                [
                    'ger' => 'Sie haben die Menge, die Sie von Artikel <mark>%s</mark> zurücksenden möchten, um'
                        . ' <mark>%s</mark> geändert.',
                    'eng' => 'You have changed the quantity that you want to send back for item <mark>%s</mark> by'
                        . ' <mark>%s</mark>.'
                ]
            , 'rmaHistoryItemAddedTitle' =>
                [
                    'ger' => 'Artikel hinzugefügt',
                    'eng' => 'Item added'
                ]
            , 'rmaHistoryItemAddedText' =>
                [
                    'ger' => 'Der Artikel <mark>%s</mark> wurde zur Retoure hinzugefügt.',
                    'eng' => 'The item <mark>%s</mark> has been added to the return.'
                ]
            , 'rmaHistoryItemRemovedTitle' =>
                [
                    'ger' => 'Artikel entfernt',
                    'eng' => 'Item removed'
                ]
            , 'rmaHistoryItemRemovedText' =>
                [
                    'ger' => 'Der Artikel <mark>%s</mark> wurde aus der Retoure entfernt.',
                    'eng' => 'The item <mark>%s</mark> has been removed from this return.'
                ]
            , 'rmaHistoryItemModifiedReasonTitle' =>
                [
                    'ger' => 'Rückgabegrund geändert',
                    'eng' => 'Return reason changed'
                ]
            , 'rmaHistoryItemModifiedReasonText' =>
                [
                    'ger' => 'Der Rückgabegrund für den Artikel <mark>%s</mark> wurde von'
                        . ' <mark>%s</mark> zu <mark>%s</mark> geändert.',
                    'eng' => 'The return reason for item <mark>%s</mark> was changed from <mark>%s</mark> to'
                        . ' <mark>%s</mark>.'
                ]
            , 'rmaHistoryReplacementOrderAssignedTitle' =>
                [
                    'ger' => 'Umtausch angestoßen',
                    'eng' => 'Exchange order in progress'
                ]
            , 'rmaHistoryReplacementOrderAssignedText' =>
                [
                    'ger' => 'Der Händler hat den Umtausch angestoßen. Die zugehörige Bestellnummer lautet'
                        . ' <mark>%s</mark>.',
                    'eng' => 'The merchant has started the process of exchanging the goods. The related order ID is'
                        . ' <mark>%s</mark>.'
                ]
            , 'rmaHistoryStatusChangedTitle' =>
                [
                    'ger' => 'Statusänderung',
                    'eng' => 'Status changed'
                ]
            , 'rmaHistoryStatusChangedText' =>
                [
                    'ger' => 'Der Status wurde von <mark>%s</mark> zu <mark>%s</mark> geändert.',
                    'eng' => 'The status was changed from <mark>%s</mark> to <mark>%s</mark>.'
                ]
            , 'rmaHistoryAddressModifiedTitle' =>
                [
                    'ger' => 'Adressänderung',
                    'eng' => 'Address changed'
                ]
            , 'rmaHistoryAddressModifiedText' =>
                [
                    'ger' => 'Die hinterlegte Adresse wurde geändert. Die vorherige Adresse lautete:<br>'
                        . '<pre class="text-wrap word-break">%s</pre>',
                    'eng' => 'The saved address was changed. The former address was:<br>'
                        . '<pre class="text-wrap word-break">%s</pre>'
                ]
            , 'rmaHistoryRefundShippingTitle' =>
                [
                    'ger' => 'Versandkostenerstattung',
                    'eng' => 'Shipping costs refund'
                ]
            , 'rmaHistoryRefundShippingText' =>
                [
                    'ger' => 'Die Erstattung der Versandkosten wurde <mark>%s</mark>.',
                    'eng' => 'The refund of shipping costs was <mark>%s</mark>.'
                ]
            , 'rmaHistoryVoucherCreditTitle' =>
                [
                    'ger' => 'Coupons und Rabatte',
                    'eng' => 'Coupon and discount'
                ]
            , 'rmaHistoryVoucherCreditText' =>
                [
                    'ger' => 'Die Erstattung von Rabatten/Coupons wurde <mark>%s</mark>.',
                    'eng' => 'The refund of discounts/coupons was <mark>%s</mark>.'
                ]
            , 'rmaHistoryRefundAccepted' =>
                [
                    'ger' => 'akzeptiert',
                    'eng' => 'accepted'
                ]
            , 'rmaHistoryRefundDenied' =>
                [
                    'ger' => 'abgelehnt',
                    'eng' => 'rejected'
                ]
            , 'youHaveNoShippingAddress' =>
                [
                    'ger' => 'Sie haben noch keine Lieferadresse hinterlegt.',
                    'eng' => 'You have not entered a shipping address yet.'
                ]
            , 'rmaNoItems' =>
                [
                    'ger' => 'Sie haben noch keine Artikel zurückgesendet.',
                    'eng' => 'You have not returned any items yet.'
                ]
            , 'rmaChangelog' =>
                [
                    'ger' => 'Changelog > Retourenverlauf',
                    'eng' => 'Changelog > Returns history'
                ]
            , 'rmaReason' =>
                [
                    'ger' => 'Rückgabegrund',
                    'eng' => 'Return reason'
                ]
            , 'rmaQuantity' =>
                [
                    'ger' => 'Menge',
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
    }

    /**
     * @inheritdoc
     */
    public function up(): void
    {
        $this->removeConfig('global_rma_enabled');
        $this->setConfig(
            configName          : 'global_rma_enabled',
            configValue         : 'N',
            configSection       : CONF_GLOBAL,
            externalName        : 'Retourenmanagement aktivieren',
            inputType           : 'selectbox',
            sort                : 651,
            additionalProperties: (object)[
                'cBeschreibung' => 'Wenn Sie diese Einstellung aktivieren, können Kunden in ihrem Kundenkonto'
                    . ' Rücksendungen anmelden und deren Bearbeitungsstatus einsehen.',
                'inputOptions'  => [
                    'Y' => 'Ja',
                    'N' => 'Nein'
                ]
            ]
        );

        foreach ($this->getLangData() as $key => $values) {
            foreach ($values as $iso => $value) {
                $this->setLocalization($iso, 'rma', $key, addslashes($value));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
    }
}
