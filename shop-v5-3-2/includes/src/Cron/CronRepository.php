<?php declare(strict_types=1);

namespace JTL\Cron;

use JTL\Abstracts\AbstractDBRepository;

/**
 * Class CronRepository
 * @package JTL\Cron
 */
class CronRepository extends AbstractDBRepository
{
    /**
     * @inheritdoc
     */
    public function getTableName(): string
    {
        return 'tcron';
    }

    /**
     * @inheritdoc
     */
    public function getKeyName(): string
    {
        return 'cronID';
    }

    /**
     * @param array $ids
     * @param array $exclude
     * @return bool
     */
    public function deleteCron(array $ids, array $exclude): bool
    {
        return $this->getDB()->queryPrepared(
            'DELETE FROM ' . $this->getTableName() . ' WHERE cronID IN (:IDs) AND jobType NOT IN (:jobTypes)',
            [
                'IDs'      => \implode(',', $ids),
                'jobTypes' => \implode(',', $exclude)
            ]
        );
    }
}
