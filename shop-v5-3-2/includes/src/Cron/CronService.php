<?php declare(strict_types=1);

namespace JTL\Cron;

use JTL\Abstracts\AbstractService;

/**
 * Class CronService
 * @package JTL\Cron
 */
class CronService extends AbstractService
{
    /**
     * @param CronRepository|null  $repository
     * @param JobQueueService|null $jobQueueService
     */
    public function __construct(
        protected ?CronRepository $repository = null,
        protected ?JobQueueService $jobQueueService = null
    ) {
        $this->repository      = $repository ?? new CronRepository();
        $this->jobQueueService = $jobQueueService ?? new JobQueueService();
    }

    /**
     * @return CronRepository
     */
    public function getRepository(): CronRepository
    {
        return $this->repository;
    }

    /**
     * @return JobQueueService
     */
    public function getJobQueueService(): JobQueueService
    {
        return $this->jobQueueService;
    }

    /**
     * @return string[]
     */
    public static function getPermanentJobTypes(): array
    {
        return [
            Type::LICENSE_CHECK,
            Type::MAILQUEUE,
        ];
    }

    /**
     * @param array $cronIDs
     * @return bool
     */
    public function delete(array $cronIDs): bool
    {
        $this->getRepository()->deleteCron($cronIDs, self::getPermanentJobTypes());

        return $this->getJobQueueService()->delete($cronIDs);
    }
}
