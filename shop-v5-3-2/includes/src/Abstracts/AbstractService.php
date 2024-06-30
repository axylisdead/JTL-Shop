<?php declare(strict_types=1);

namespace JTL\Abstracts;

use JTL\DataObjects\AbstractDomainObject;
use JTL\DataObjects\DomainObjectInterface;
use JTL\Exceptions\CircularReferenceException;
use JTL\Exceptions\ServiceNotFoundException;
use JTL\Shop;

/**
 * Class AbstractService
 * @package JTL\Abstracts
 */
abstract class AbstractService
{
    /**
     * @param string $msg
     * @param array  $param
     * @param string $type
     * @return void
     * @throws CircularReferenceException
     * @throws ServiceNotFoundException
     */
    protected function log(string $msg, array $param = [], string $type = 'info'): void
    {
        match ($type) {
            'warning' => Shop::Container()->getLogService()->warning($msg, $param),
            'error'   => Shop::Container()->getLogService()->error($msg, $param),
            default   => Shop::Container()->getLogService()->info($msg, $param)
        };
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getList(array $filters): array
    {
        return $this->repository->getList($filters);
    }

    /**
     * @param AbstractDomainObject $insertDTO
     * @return int
     */
    public function insert(AbstractDomainObject $insertDTO): int
    {
        if ($insertDTO instanceof DomainObjectInterface) {
            return $this->repository->insert($insertDTO);
        }

        return 0;
    }

    /**
     * @param AbstractDomainObject $updateDO
     * @return bool
     */
    public function update(AbstractDomainObject $updateDO): bool
    {
        if ($updateDO->getID() > 0) {
            return $this->repository->update($updateDO);
        }

        return false;
    }
}
