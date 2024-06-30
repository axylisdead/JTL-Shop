<?php declare(strict_types=1);

namespace JTL\RMA\Repositories;

use JTL\Abstracts\AbstractDBRepository;
use JTL\RMA\DomainObjects\RMAItemDomainObject;

/**
 * Class RMAItemRepository
 * @package JTL\RMA\Repositories
 * @description This is a layer between the RMA Service (products) and the database.
 */
class RMAItemRepository extends AbstractDBRepository
{
    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'rma_items';
    }

    /**
     * @param RMAItemDomainObject $domainObject
     * @return bool
     */
    public function updateByShippingNotePosID(RMAItemDomainObject $domainObject): bool
    {
        return ($this->db->updateRow(
            $this->getTableName(),
            ['shippingNotePosID', 'productID'],
            [$domainObject->shippingNotePosID, $domainObject->productID],
            $domainObject->toObject()
        ) !== self::UPDATE_OR_UPSERT_FAILED);
    }
}
