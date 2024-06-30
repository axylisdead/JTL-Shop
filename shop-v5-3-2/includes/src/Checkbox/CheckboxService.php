<?php declare(strict_types=1);

namespace JTL\Checkbox;

use JTL\Abstracts\AbstractService;
use JTL\CheckBox;
use JTL\Checkbox\CheckboxLanguage\CheckboxLanguageService;
use JTL\Helpers\Typifier;

/**
 * Class CheckboxService
 * @package JTL\Checkbox
 */
class CheckboxService extends AbstractService
{
    /**
     * @param CheckboxRepository|null      $repository
     * @param CheckboxLanguageService|null $languageService
     */
    public function __construct(
        protected ?CheckboxRepository $repository = null,
        protected ?CheckboxLanguageService $languageService = null
    ) {
        $this->repository      = $this->repository ?? new CheckboxRepository();
        $this->languageService = $this->languageService ?? new CheckboxLanguageService();
    }

    /**
     * @param array $data
     * @param array $languages
     * @return CheckboxDomainObject
     */
    public function getCheckBoxDomainObject(array $data, array $languages): CheckboxDomainObject
    {
        if ($languages === []) {
            $languages = $this->languageService->getLanguagesByCheckboxID((int)$data['kCheckBox']);
        }
        $data['languages'] = $this->prepareTranslationsForDO($languages, $data);
        if (!isset($data['nLink']) || $data['nLink'] === false || (int)$data['nLink'] === -1) {
            $data['kLink'] = 0;
        }

        $customerGroupsTmp = $data['cKundengruppe'] ?? $data['kKundengruppe'];
        if (\is_array($customerGroupsTmp) === true) {
            $customerGroups = $this->implodeWithAdditionalSeparators($customerGroupsTmp, ';', true);
        } else {
            $customerGroups = $customerGroupsTmp;
        }

        return new CheckboxDomainObject(
            Typifier::intify($data['kCheckBox'] ?? 0),
            Typifier::intify($data['kLink'] ?? 0),
            Typifier::intify($data['kCheckBoxFunktion'] ?? 0),
            Typifier::stringify($data['cName'] ?? ''),
            $customerGroups,
            \is_array($data['cAnzeigeOrt']) === true
                ? $this->implodeWithAdditionalSeparators($data['cAnzeigeOrt'] ?? [], ';', true)
                : $data['cAnzeigeOrt'],
            Typifier::boolify($data['nAktiv'] ?? false),
            Typifier::boolify($data['nPflicht'] ?? false),
            Typifier::boolify($data['nLogging'] ?? false),
            Typifier::intify($data['nSort'] ?? 0),
            Typifier::stringify($data['dErstellt'] ?? 'now()'),
            Typifier::boolify($data['nInternal'] ?? false),
            Typifier::stringify(($data['dErstellt_DE'] ?? '')),
            Typifier::arrify($data['languages'] ?? []),
            Typifier::boolify($data['cLink'] ?? false),
            Typifier::arrify($data['oCheckBoxSprache_arr'] ?? []),
            Typifier::arrify($data['kKundengruppe_arr'] ?? []),
            Typifier::arrify($data['kAnzeigeOrt_arr'] ?? [])
        );
    }

    /**
     * @param array $languages
     * @param array $post
     * @return array
     */
    private function prepareTranslationsForDO(array $languages, array $post): array
    {
        $collected = [];
        foreach ($languages as $language) {
            $code             = $language->getIso();
            $textCode         = 'cText_' . $code;
            $descrCode        = 'cBeschreibung_' . $code;
            $texts[$code]     = isset($post[$textCode])
                ? \str_replace('"', '&quot;', $post[$textCode])
                : '';
            $descr[$code]     = isset($post[$descrCode])
                ? \str_replace('"', '&quot;', $post[$descrCode])
                : '';
            $collected[$code] = [
                'text'  => $texts[$code],
                'descr' => $descr[$code]
            ];
        }

        return $collected;
    }

    /**
     * @param int $id
     * @return ?CheckboxDomainObject
     */
    public function get(int $id): ?CheckboxDomainObject
    {
        $data = $this->repository->get($id);
        if ($data === null) {
            return null;
        }
        if ((int)$data->kLink > 0) {
            $data->nLink = true;
        }

        return $this->getCheckBoxDomainObject(\json_decode(\json_encode($data), true), []);
    }

    /**
     * @param int[] $checkboxIDs
     * @return bool
     */
    public function activate(array $checkboxIDs): bool
    {
        return $this->repository->activate($checkboxIDs);
    }

    /**
     * @param int[] $checkboxIDs
     * @return bool
     */
    public function deactivate(array $checkboxIDs): bool
    {
        return $this->repository->deactivate($checkboxIDs);
    }

    /**
     * @param array $checkboxIDs
     * @return bool
     */
    public function deleteByIDs(array $checkboxIDs): bool
    {
        return $this->repository->deleteByIDs($checkboxIDs);
    }


    /**
     * @param CheckboxValidationDomainObject $data
     * @param array                          $post
     * @return array
     */
    public function validateCheckBox(CheckboxValidationDomainObject $data, array $post): array
    {
        $checks = [];
        foreach ($this->getCheckBoxValidationData($data) as $checkBox) {
            if ($checkBox->nPflicht === 1 && !isset($post[$checkBox->cID])) {
                if ($checkBox->cName === CheckBox::CHECKBOX_DOWNLOAD_ORDER_COMPLETE
                    && $data->getHasDownloads() === false) {
                    continue;
                }
                $checks[$checkBox->cID] = 1;
            }
        }

        return $checks;
    }

    /**
     * @param CheckboxValidationDomainObject $data
     * @return CheckBox[]
     */
    public function getCheckBoxValidationData(CheckboxValidationDomainObject $data): array
    {
        $checkboxes = $this->repository->getCheckBoxValidationData($data);
        \executeHook(\HOOK_CHECKBOX_CLASS_GETCHECKBOXFRONTEND, [
            'oCheckBox_arr' => &$checkboxes,
            'nAnzeigeOrt'   => $data->getLocation(),
            'kKundengruppe' => $data->getCustomerGroupId(),
            'bAktiv'        => $data->getActive(),
            'bSprache'      => $data->getLanguage(),
            'bSpecial'      => $data->getSpecial(),
            'bLogging'      => $data->getLogging(),
        ]);

        return $checkboxes;
    }

    /**
     * @param mixed  $value
     * @param string $separator
     * @param bool   $addLeadingAndTrailingSeparator
     * @return string
     */
    private function implodeWithAdditionalSeparators(
        array $value,
        string $separator = ';',
        bool $addLeadingAndTrailingSeparator = false
    ): string {
        if ($addLeadingAndTrailingSeparator === true) {
            $result = $separator . \implode($separator, $value) . $separator;
        } else {
            $result = \implode($separator, $value);
        }

        return $result;
    }
}
