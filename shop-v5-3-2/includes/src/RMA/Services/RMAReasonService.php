<?php declare(strict_types=1);

namespace JTL\RMA\Services;

use Exception;
use JTL\Abstracts\AbstractService;
use JTL\Helpers\Typifier;
use JTL\RMA\DomainObjects\RMAReasonLangDomainObject;
use JTL\RMA\Repositories\RMAReasonLangRepository;
use JTL\RMA\Repositories\RMAReasonRepository;

/**
 * Class RMAReasonService
 * @package JTL\RMA
 */
class RMAReasonService extends AbstractService
{
    /**
     * @var RMAReasonLangDomainObject[]|null
     */
    public ?array $reasons = null;

    /**
     * @param RMAReasonRepository|null $RMAReasonRepository
     * @param RMAReasonLangRepository|null $RMAReasonLangRepository
     */
    public function __construct(
        public ?RMAReasonRepository $RMAReasonRepository = null,
        public ?RMAReasonLangRepository $RMAReasonLangRepository = null,
    ) {
        $this->RMAReasonRepository     = $RMAReasonRepository ?? new RMAReasonRepository();
        $this->RMAReasonLangRepository = $RMAReasonLangRepository ?? new RMAReasonLangRepository();
    }

    /**
     * @param int $langID
     * @return $this
     * @throws Exception
     * @since 5.3.0
     */
    public function loadReasons(int $langID): self
    {
        foreach ($this->RMAReasonLangRepository->getList(['langID' => $langID]) as $reason) {
            $this->reasons[$reason->reasonID] = new RMAReasonLangDomainObject(
                id: Typifier::intify($reason->id ?? 0),
                reasonID: Typifier::intify($reason->reasonID ?? 0),
                langID: Typifier::intify($reason->langID ?? 0),
                title: Typifier::stringify($reason->title ?? '')
            );
        }

        return $this;
    }

    /**
     * @param int $id
     * @param int $languageID
     * @return RMAReasonLangDomainObject
     * @throws Exception
     * @since 5.3.0
     */
    public function getReason(int $id, int $languageID): RMAReasonLangDomainObject
    {
        if (!isset($this->reasons)) {
            $this->loadReasons($languageID);
        }

        return $this->reasons[$id] ?? new RMAReasonLangDomainObject();
    }
}
