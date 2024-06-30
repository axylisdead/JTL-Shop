<?php declare(strict_types=1);

namespace JTL\RMA\Services;

use Exception;
use JTL\Abstracts\AbstractService;
use JTL\RMA\DomainObjects\RMADomainObject;
use JTL\RMA\DomainObjects\RMAItemDomainObject;
use JTL\RMA\DomainObjects\RMAReturnAddressDomainObject;
use JTL\RMA\Helper\RMAItems;
use JTL\RMA\Repositories\RMAItemRepository;
use JTL\RMA\Repositories\RMARepository;
use JTL\Session\Frontend;
use JTL\Shop;
use JTL\Shopsetting;
use stdClass;

/**
 * Class RMAService
 * @package JTL\RMA
 * @since 5.3.0
 * @todo Add caching functionality to the whole RMA related code after it gets a releasable state. Every call to a
 * @todo repository should be cached. What about creating mail templates?
 */
class RMAService extends AbstractService
{
    /**
     * @var RMADomainObject[]
     */
    public array $rmas = [];

    /**
     * @var RMAItems|null
     */
    private readonly ?RMAItems $returnableProducts;

    /**
     * @param RMARepository|null $RMARepository
     * @param RMAItemRepository|null $RMAItemRepository
     * @param RMAReturnAddressService|null $RMAReturnAddressService
     */
    public function __construct(
        private ?RMARepository $RMARepository = null,
        private ?RMAItemRepository $RMAItemRepository = null,
        public ?RMAReturnAddressService $RMAReturnAddressService = null
    ) {
        $this->RMARepository           = $RMARepository ?? new RMARepository();
        $this->RMAItemRepository       = $RMAItemRepository ?? new RMAItemRepository();
        $this->RMAReturnAddressService = $RMAReturnAddressService ?? new RMAReturnAddressService();
    }

    /**
     * @param int $langID
     * @param array $filter
     * @param int|null $limit
     * @return self
     * @throws Exception
     */
    public function loadReturns(int $langID, array $filter = [], ?int $limit = null): self
    {
        $this->rmas = $this->RMARepository->getReturns(
            $langID,
            $filter,
            $limit
        );

        return $this;
    }

    /**
     * @param int $id
     * @param int|null $customerID
     * @param int|null $langID
     * @return RMADomainObject
     * @throws Exception
     */
    public function getReturn(int $id, ?int $customerID = null, ?int $langID = null): RMADomainObject
    {
        if ($id === 0) {
            return new RMADomainObject();
        }
        if (isset($this->rmas[$id])) {
            return $this->rmas[$id];
        }

        if ($customerID !== null && $langID !== null) {
            $this->rmas = $this->RMARepository->getReturns(
                $langID,
                [
                    'customerID' => $customerID,
                    'id'         => $id
                ]
            );
        }

        return $this->rmas[$id] ?? new RMADomainObject();
    }

    /**
     * @param int $customerID
     * @return RMADomainObject
     * @throws Exception
     */
    public function newReturn(int $customerID): RMADomainObject
    {
        return new RMADomainObject(
            customerID: $customerID,
        );
    }

    /**
     * @param RMADomainObject[] $rmas
     * @return RMAItems[]
     * @description List all items for every RMA passed by argument
     */
    public function getRMAItems(?array $rmas = null): array
    {
        $result = [];
        $rmas   = $rmas ?? $this->rmas;

        foreach ($rmas as $rma) {
            $result[] = $rma->getRMAItems();
        }

        return $result;
    }

    /**
     * @param RMAItems $rmaItems
     * @return array
     * @description Returns an array with order IDs as keys and order number + date as values
     */
    public function getOrderArray(RMAItems $rmaItems): array
    {
        $result = [];
        foreach ($rmaItems->getArray() as $pos) {
            if (isset($result[$pos->orderID])) {
                continue;
            }
            $result[$pos->orderID] = [
                'orderNo'   => $pos->getOrderNo(),
                'orderDate' => $pos->getOrderDate('d.m.Y')
            ];
        }

        return $result;
    }

    /**
     * @param array $orderIDs
     * @return array
     * @description Returns an array with order IDs as keys and order numbers as values
     */
    public function getOrderNumbers(array $orderIDs): array
    {
        return \count($orderIDs) > 0
            ? $this->RMARepository->getOrderNumbers($orderIDs)
            : [];
    }

    /**
     * @param RMAItems $rmaItems
     * @param string   $by
     * @return RMAItems
     * @description Groups RMA-Items together by a given key
     */
    public function groupRMAItems(RMAItems $rmaItems, string $by = 'order'): RMAItems
    {
        $result      = new RMAItems();
        $allowedKeys = [
            'order'   => 'orderNo',
            'product' => 'productID',
            'reason'  => 'reasonID',
            'status'  => 'status',
            'day'     => 'createDate'
        ];
        $groupByKey  = $allowedKeys[$by] ?? 'orderNo';

        foreach ($rmaItems->getArray() as $item) {
            $getterMethod = 'get' . \ucfirst($groupByKey);
            if (\method_exists($item, $getterMethod) !== false) {
                $newKey = $item->{$getterMethod}();
            } else {
                $newKey = $item->{$groupByKey};
            }
            if ($by === 'day') {
                $newKey = \date('d.m.Y', \strtotime($newKey));
            }
            $result[$newKey][] = $item;
        }

        return $result;
    }

    /**
     * @param int $customerID
     * @param int $languageID
     * @param int $cancellationTime
     * @param int $orderID
     * @param bool $forceDB
     * @return RMAItems
     * @throws Exception
     * @description Returns a list of returnable products for a given customer and/or order
     * @since 5.3.0
     */
    public function getReturnableProducts(
        int $customerID = 0,
        int $languageID = 0,
        int $cancellationTime = 0,
        int $orderID = 0,
        bool $forceDB = false
    ): RMAItems {
        if ($forceDB || !isset($this->returnableProducts)) {
            $customerID       = $customerID ?: Frontend::getCustomer()->getID();
            $languageID       = $languageID ?: Shop::getLanguageID();
            $cancellationTime = $cancellationTime ?: Shopsetting::getInstance()->getValue(
                sectionID: \CONF_GLOBAL,
                option: 'global_cancellation_time'
            );

            $this->returnableProducts = new RMAItems(
                $this->RMARepository->getReturnableProducts(
                    customerID: $customerID,
                    languageID: $languageID,
                    cancellationTime: $cancellationTime,
                    orderID: $orderID
                )
            );
        }

        return $this->returnableProducts;
    }

    /**
     * @param RMADomainObject $rma
     * @param int $shippingNotePosID
     * @return RMAItemDomainObject
     * @throws Exception
     * @comment Is used directly in Smarty template
     * @since 5.3.0
     */
    public function getRMAItem(RMADomainObject $rma, int $shippingNotePosID): RMAItemDomainObject
    {
        return $rma->getRMAItems()->getItem($shippingNotePosID) ?? new RMAItemDomainObject();
    }

    /**
     * @param int $statusID
     * @return string
     * @description  Translate RMA status text
     * @since 5.3.0
     */
    public function getStatusTextByID(int $statusID): string
    {
        return match ($statusID) {
            \RETURN_STATUS_ACCEPTED    => Shop::Lang()->get('statusAccepted', 'rma'),
            \RETURN_STATUS_REJECTED    => Shop::Lang()->get('statusRejected', 'rma'),
            \RETURN_STATUS_COMPLETED   => Shop::Lang()->get('statusCompleted', 'rma'),
            \RETURN_STATUS_OPEN,       => Shop::Lang()->get('statusOpen', 'rma'),
            \RETURN_STATUS_IN_PROGRESS => Shop::Lang()->get('statusProcessing', 'rma')
        };
    }

    /**
     * @param RMADomainObject $rma
     * @return stdClass
     * @since 5.3.0
     */
    public function getStatus(RMADomainObject $rma): stdClass
    {
        $result        = new stdClass();
        $result->text  = $this->getStatusTextByID($rma->status);
        $result->class = match ($rma->status) {
            \RETURN_STATUS_ACCEPTED, \RETURN_STATUS_IN_PROGRESS => 'info',
            \RETURN_STATUS_COMPLETED                            => 'success',
            \RETURN_STATUS_OPEN,                                => 'warning',
            \RETURN_STATUS_REJECTED                             => 'danger',
        };

        return $result;
    }

    /**
     * @param RMADomainObject $rma
     * @return int
     * @throws Exception
     * @description Prepare domain objects and do the insert
     * @since 5.3.0
     */
    public function insertRMA(RMADomainObject $rma): int
    {
        if ($rma->id > 0) {
            // Maybe should not perform an insert but instead an update?
            return 0;
        }

        $rmaID = $this->RMARepository->insert($rma);
        if ($rmaID > 0) {
            foreach ($rma->getRMAItems()->getArray() as $item) {
                if ($this->RMAItemRepository->insert(
                    new RMAItemDomainObject(...$item->copyWith(['rmaID' => $rmaID])->toArray())
                ) === 0) {
                    // @todo Maybe throw an exception here?
                    $this->log(
                        msg: 'Could not insert RMA-Position while inserting RMA with id {id}',
                        param: ['id' => $rmaID],
                        type: 'warning'
                    );
                }
            }

            $returnAddress = $rma->getReturnAddress();
            // @todo Maybe we should talk about creating a new DO after modifying the old one?
            if (($returnAddress !== null)
                && $this->RMAReturnAddressService->RMAReturnAddressRepository->insert(
                    new RMAReturnAddressDomainObject(
                        ...$returnAddress->copyWith(['rmaID' => $rmaID])->toArray()
                    )
                ) === 0) {
                // @todo Maybe throw an exception here?
                    $this->log(
                        msg: 'Could not insert RMA Return address while inserting RMA with id {id}',
                        param: ['id' => $rma->id],
                        type: 'warning'
                    );
            }
        }

        return $rmaID;
    }

    /**
     * @param RMADomainObject $rma
     * @return string
     * @since 5.3.0
     */
    public function hashCreateDate(RMADomainObject $rma): string
    {
        return \md5($rma->createDate);
    }

    /**
     * @param int $orderID
     * @param RMAItems|null $returnableProducts
     * @return bool
     * @since 5.3.0
     */
    public function isOrderReturnable(
        int $orderID,
        ?RMAItems $returnableProducts = null
    ): bool {
        if ($returnableProducts !== null) {
            foreach ($returnableProducts->getArray() as $item) {
                if ($item->orderID === $orderID) {
                    return true;
                }
            }

            return false;
        }

        return \count(
            $this->RMARepository->getReturnableProducts(
                customerID: Frontend::getCustomer()->getID(),
                languageID: Shop::getLanguageID(),
                cancellationTime: Shopsetting::getInstance()->getValue(
                    sectionID: \CONF_GLOBAL,
                    option: 'global_cancellation_time'
                ),
                orderID: $orderID
            )
        ) > 0;
    }

    /**
     * @param string|null $date
     * @return string
     * @since 5.3.0
     * @comment Used in templates/NOVA/account/rmas.tpl
     */
    public static function localizeDate(?string $date): string
    {
        $result = '';
        if ($date !== null && \mb_strlen($date) > 1) {
            $date = \str_replace('.', '-', $date);
            try {
                $result = (new \DateTime($date))->format('d.m.Y');
            } catch (Exception) {
                $result = '00.00.0000';
            }
        }

        return $result;
    }
}
