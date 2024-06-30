<?php declare(strict_types=1);

namespace JTL\Cron;

/**
 * Class JobHydrator
 * @package JTL\Cron
 */
final class JobHydrator
{
    /**
     * @var string[]
     */
    private static array $mapping = [
        'cronID'        => 'CronID',
        'jobType'       => 'Type',
        'taskLimit'     => 'Limit',
        'tasksExecuted' => 'Executed',
        'foreignKeyID'  => 'ForeignKeyID',
        'foreignKey'    => 'ForeignKey',
        'tableName'     => 'TableName',
        'jobQueueID'    => 'QueueID',
        'lastStart'     => 'DateLastStarted',
        'startTime'     => 'StartTime',
        'frequency'     => 'Frequency',
        'isRunning'     => 'Running',
        'lastFinish'    => 'DateLastFinished',
        'nextStart'     => 'NextStartDate'
    ];

    /**
     * @param string $key
     * @return string|null
     */
    private function getMapping(string $key): ?string
    {
        return self::$mapping[$key] ?? null;
    }

    /**
     * @param JobInterface $class
     * @param object       $data
     * @return object
     */
    public function hydrate(JobInterface $class, object $data)
    {
        foreach (\get_object_vars($data) as $key => $value) {
            if (($mapping = $this->getMapping($key)) === null) {
                continue;
            }
            $method = 'set' . $mapping;
            $class->$method($value);
        }

        return $class;
    }
}
