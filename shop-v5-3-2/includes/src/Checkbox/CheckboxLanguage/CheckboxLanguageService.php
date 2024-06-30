<?php declare(strict_types=1);

namespace JTL\Checkbox\CheckboxLanguage;

use JTL\Abstracts\AbstractService;
use JTL\DataObjects\AbstractDomainObject;
use JTL\Helpers\Typifier;

/**
 * Class CheckboxLanguageService
 * @package JTL\Checkbox\CheckboxLanguage
 */
class CheckboxLanguageService extends AbstractService
{
    /**
     * @param CheckboxLanguageRepository|null $repository
     */
    public function __construct(protected ?CheckboxLanguageRepository $repository = null)
    {
        $this->repository = $this->repository ?? new CheckboxLanguageRepository();
    }

    private function getCheckboxLanguageDomainObject(array $language): CheckboxLanguageDomainObject
    {
        $language['ISO'] = $language['ISO'] ?? '';

        return new CheckboxLanguageDomainObject(
            Typifier::intify($language['kCheckBox'] ?? null),
            Typifier::intify($language['kCheckBoxSprache'] ?? null),
            Typifier::intify($language['kSprache'] ?? null),
            Typifier::stringify($language['ISO'] ?? null),
            Typifier::stringify($language['cText'] ?? null),
            Typifier::stringify($language['cBeschreibung'] ?? null)
        );
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getList(array $filters): array
    {
        $languageList = [];
        foreach ($this->repository->getList($filters) as $checkboxLanguage) {
            $languageList[] = $this->getCheckboxLanguageDomainObject((array)$checkboxLanguage);
        }

        return $languageList;
    }

    public function getLanguagesByCheckboxID(int $checkBoxID): array
    {
        $objects = [];
        foreach ($this->repository->getLanguagesByCheckboxID($checkBoxID) as $language) {
            $objects[$language['kSprache']] = $this->getCheckboxLanguageDomainObject($language);
        }

        return $objects;
    }

    /**
     * @param AbstractDomainObject $updateDO
     * @return bool
     */
    public function update(AbstractDomainObject $updateDO): bool
    {
        if (!$updateDO instanceof CheckboxLanguageDomainObject) {
            return false;
        }
        // need checkboxLanguageId, not provided by post
        $languageList = $this->getList([
            'kCheckBox' => $updateDO->getCheckboxID(),
            'kSprache'  => $updateDO->getLanguageID()
        ]);
        $language     = $languageList[0] ?? null;
        if ($language === null) {
            return $this->insert($updateDO) > 0;
        }

        return $this->repository->update($updateDO, $language->getCheckboxLanguageID());
    }

    private function getLanguageListForCheckbox(int $checkboxID, int $languageID): ?array
    {
        return $this->getList([
            'kCheckBox' => $checkboxID,
            'kSprache'  => $languageID
        ]);
    }
}
