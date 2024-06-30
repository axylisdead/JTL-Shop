<?php declare(strict_types=1);

namespace JTL\dbeS\Sync;

use Exception;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\dbeS\Starter;
use JTL\Helpers\Typifier;
use JTL\Language\LanguageHelper;
use JTL\RMA\DomainObjects\RMADomainObject;
use JTL\RMA\DomainObjects\RMAItemDomainObject;
use JTL\RMA\DomainObjects\RMAReturnAddressDomainObject;
use JTL\RMA\Helper\RMAItems;
use JTL\RMA\Services\RMAHistoryService;
use JTL\RMA\Services\RMAService;
use JTL\XML;
use Psr\Log\LoggerInterface;

/**
 * Class Orders
 * @package JTL\dbeS\Sync
 * @since 5.3.0
 */
final class Returns extends AbstractSync
{

    private RMAService $rmaService;
    private RMAHistoryService $rmaHistoryService;

    /**
     * AbstractSync constructor.
     *
     * @param DbInterface       $db
     * @param JTLCacheInterface $cache
     * @param LoggerInterface   $logger
     */
    public function __construct(
        protected DbInterface       $db,
        protected JTLCacheInterface $cache,
        protected LoggerInterface   $logger
    ) {
        parent::__construct($db, $cache, $logger);
        $this->rmaService        = new RMAService();
        $this->rmaHistoryService = new RMAHistoryService();
    }

    /**
     * @param Starter $starter
     * @return null
     * @throws Exception
     * @since 5.3.0
     */
    public function handle(Starter $starter)
    {
        foreach ($starter->getXML() as $xml) {
            \reset($xml);
            if ($xml === null) {
                $this->logger->error('No returns found in XML: '
                    . XML::getLastParseError() . ' in:' . \print_r($xml, true));

                return null;
            }
            foreach ($xml as $returns) {
                foreach ($returns['rmas'] as $return) {
                    $rmaDomainObject = $this->createRmaDomainObject($return);
                    if ($rmaDomainObject->id > 0) {
                        $this->update($rmaDomainObject);
                    } else {
                        $this->insert($rmaDomainObject);
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param RMADomainObject $rmaDomainObject
     * @return void
     * @throws Exception
     * @since 5.3.0
     */
    private function insert(RMADomainObject $rmaDomainObject): void
    {
    }

    /**
     * @param RMADomainObject $rmaDomainObject
     * @throws Exception
     * @since 5.3.0
     */
    private function update(RMADomainObject $rmaDomainObject): void
    {
        $originalDomainObject = $this->rmaService->getReturn(
            id        : $rmaDomainObject->id,
            customerID: $rmaDomainObject->customerID,
            langID    : LanguageHelper::getDefaultLanguage()->id
        );
        /*
        if ($rmaDomainObject->id > 0) {
            if ($this->rmaService->RMARepository->update($rmaDomainObject) === false) {
                $this->logger->error('Could not update RMA. XML: '
                    . XML::getLastParseError() . ' in:' . \print_r($return, true));
                return;
            }
        } else {
            $rmaDomainObject = $rmaDomainObject->modifyValue(
                'id',
                $this->rmaService->RMARepository->insert($rmaDomainObject)
            );
            if ($rmaDomainObject->id === 0) {
                $this->logger->error('Could not insert RMA. XML: '
                    . XML::getLastParseError() . ' in:' . \print_r($return, true));
                return;
            }
        }
        */

        $this->rmaHistoryService->dispatchEvents($originalDomainObject, $rmaDomainObject);
    }

    /**
     * @param mixed $return
     * @return RMADomainObject
     * @throws Exception
     * @since 5.3.0
     */
    private function createRmaDomainObject(mixed $return): RMADomainObject
    {
        $shippingNotePosIDs = [];
        if (!isset($return['item'][0])) {
            $return['item'] = [$return['item']];
        }
        foreach ($return['item'] as $item) {
            if ($item['kLieferscheinPos'] === null) {
                continue;
            }
            $shippingNotePosID = Typifier::intify($item['kLieferscheinPos'] ?? 0);
            if ($shippingNotePosID > 0) {
                $shippingNotePosIDs[$shippingNotePosID] = $shippingNotePosID;
            }
        }

        $itemData = [];
        $this->db->getCollection(
            'SELECT twarenkorbpos.cName, twarenkorbpos.kArtikel, twarenkorbpos.fMwSt,
                       twarenkorbpos.fPreisEinzelNetto, tlieferscheinpos.kLieferscheinPos
                FROM tlieferscheinpos
                LEFT JOIN twarenkorbpos
                    ON tlieferscheinpos.kBestellPos = twarenkorbpos.kBestellPos
                WHERE tlieferscheinpos.kLieferscheinPos IN (' . \implode(',', $shippingNotePosIDs) . ')'
        )->each(function ($obj) use (&$itemData) {
            $product               = new \stdClass();
            $product->name         = $obj->cName;
            $product->vat          = $obj->fMwSt;
            $product->unitPriceNet = $obj->fPreisEinzelNetto;

            $itemData[Typifier::intify($obj->kLieferscheinPos)] = $product;
        });
        $rmaDomainObject = new RMADomainObject(
            id: Typifier::intify($return['cShopID'] ?? 0),
            wawiID: Typifier::intify($return['kRMRetoure'] ?? null),
            customerID: Typifier::intify($return['kKundeShop'] ?? 0),
            replacementOrderID: null,
            rmaNr: Typifier::stringify($return['cRetoureNr'] ?? null, null),
            voucherCredit: Typifier::boolify($return['nKuponGutschriftGutschreiben'] ?? false, null),
            refundShipping: Typifier::boolify($return['nVersandkostenErstatten'] ?? false, null),
            synced: true,
            status: 1,
            comment: '',
            createDate: Typifier::stringify($return['dErstellt'] ?? null, null)
        );

        if (!isset($return['item'][0])) {
            $return['item'] = [$return['item']];
        }
        $items = new RMAItems();

        foreach ($return['item'] as $item) {
            $shippingNotePosID = Typifier::intify($item['kLieferscheinPos']);
            $items->append(new RMAItemDomainObject(
                id: 0,
                rmaID: $rmaDomainObject->id,
                shippingNotePosID: $shippingNotePosID,
                orderID: 0,
                orderPosID: 0,
                productID: Typifier::intify($item['kArtikel'] ?? null),
                reasonID: Typifier::intify($item['kRMGrund'] ?? null),
                name: Typifier::stringify($itemData[$shippingNotePosID]->name ?? ''),
                variationProductID: null,
                variationName: null,
                variationValue: null,
                partListProductID: null,
                partListProductName: null,
                partListProductURL: null,
                partListProductNo: null,
                unitPriceNet: Typifier::floatify($itemData[$shippingNotePosID]->unitPriceNet ?? null),
                quantity: Typifier::floatify($item['fAnzahl'] ?? null, 1.00),
                vat: Typifier::floatify($itemData[$shippingNotePosID]->vat ?? null),
                unit: null,
                comment: null,
                status: null,
                createDate: Typifier::stringify($item['dErstellt'] ?? null, null)
            ));
        }
        $rmaDomainObject = $rmaDomainObject->copyWith(['items' => $items]);

        // Prepare data for return address DO
        $adresse = $return['adresse'] ?? [];
        // @todo "cHausnummer" Wird aktuell nicht von der WaWi gesendet
        $adresse['cHausnummer'] = '';

        $rmaAddressDomainObject = new RMAReturnAddressDomainObject(
            id: 0,
            rmaID: $rmaDomainObject->id,
            customerID: Typifier::intify($return['kKundeShop'] ?? 0),
            salutation: Typifier::stringify($adresse['cAnrede'] ?? ''),
            firstName: Typifier::stringify($adresse['cVorname'] ?? ''),
            lastName: Typifier::stringify($adresse['cName'] ?? ''),
            academicTitle: Typifier::stringify($adresse['cTitel'] ?? null, null),
            companyName: Typifier::stringify($adresse['cFirma'] ?? null, null),
            companyAdditional: Typifier::stringify($adresse['cZusatz'] ?? null, null),
            street: Typifier::stringify($adresse['cStrasse'] ?? ''),
            houseNumber: Typifier::stringify($adresse['cHausnummer'] ?? ''),
            addressAdditional: Typifier::stringify($adresse['cAdressZusatz'] ?? null, null),
            postalCode: Typifier::stringify($adresse['cPLZ'] ?? ''),
            city: Typifier::stringify($adresse['cOrt'] ?? ''),
            state: Typifier::stringify($adresse['cBundesland'] ?? ''),
            countryISO: Typifier::stringify($adresse['cISO'] ?? ''),
            phone: Typifier::stringify($adresse['cTel'] ?? null, null),
            mobilePhone: Typifier::stringify($adresse['cMobil'] ?? null, null),
            fax: Typifier::stringify($adresse['cFax'] ?? null, null),
            mail: Typifier::stringify($adresse['cMail'] ?? null, null),
        );

        return $rmaDomainObject->copyWith(['returnAddress' => $rmaAddressDomainObject]);
    }
}
