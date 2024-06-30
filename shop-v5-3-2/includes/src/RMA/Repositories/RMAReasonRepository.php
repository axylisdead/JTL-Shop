<?php declare(strict_types=1);

namespace JTL\RMA\Repositories;

use JTL\Abstracts\AbstractDBRepository;

/**
 * Class RMAReasonRepository
 * @package JTL\RMA\Repositories
 * @description This is a layer between the RMA Reason Service and the database.
 */
class RMAReasonRepository extends AbstractDBRepository
{
    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'rma_reasons';
    }

    /**
     * @param int      $langID
     * @param int|null $productTypeGroupID
     * @return array
     */
    public function getAllReasonsLocalized(int $langID, ?int $productTypeGroupID = null): array
    {
        $params = ['langID' => $langID];
        $where  = 'langID = :langID';
        if ($productTypeGroupID !== null) {
            $where                        = ' AND productTypeGroupID = :productTypeGroupID';
            $params['productTypeGroupID'] = $productTypeGroupID;
        }

        return $this->db->getObjects(
            'SELECT rma_reasons.id, rma_reasons.wawiID, rma_reasons.wawiID, rma_reasons_lang.title
            FROM rma_reasons
            JOIN rma_reasons_lang
                ON rma_reasons_lang.reasonID = rma_reasons.id
            WHERE ' . $where,
            $params
        );
    }
}
