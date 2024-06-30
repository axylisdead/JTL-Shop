<?php declare(strict_types=1);

namespace JTL\Checkbox\CheckboxFunction;

use JTL\Abstracts\AbstractService;

/**
 * Class CheckboxFunctionService
 * @package JTL\Checkbox\CheckboxFunction
 */
class CheckboxFunctionService extends AbstractService
{
    /**
     * @var CheckboxFunctionRepository
     */
    public function __construct(protected ?CheckboxFunctionRepository $repository = null)
    {
        $this->repository = $this->repository ?? new CheckboxFunctionRepository();
    }

    public function get(int $ID): ?\stdClass
    {
        return $this->repository->get($ID);
    }
}
