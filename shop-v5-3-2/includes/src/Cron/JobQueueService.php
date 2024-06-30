<?php declare(strict_types=1);

namespace JTL\Cron;

use JTL\Abstracts\AbstractService;

/**
 * Class JobQueueService
 * @package JTL\Cron
 */
class JobQueueService extends AbstractService
{
    /**
     * @param JobQueueRepository|null $repository
     */
    public function __construct(
        protected ?JobQueueRepository $repository = null
    ) {
        $this->repository = $repository ?? new JobQueueRepository();
    }

    /**
     * @return JobQueueRepository
     */
    public function getRepository(): JobQueueRepository
    {
        return $this->repository;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool
    {
        return $this->getRepository()->deleteCron($ids, CronService::getPermanentJobTypes());
    }
}
