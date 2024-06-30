<?php declare(strict_types=1);

namespace JTL;

use Exception;
use InvalidArgumentException;
use JTL\Cache\JTLCacheInterface;
use JTL\Checkbox\CheckboxDataTableObject;
use JTL\Checkbox\CheckboxDomainObject;
use JTL\Checkbox\CheckboxFunction\CheckboxFunctionDomainObject;
use JTL\Checkbox\CheckboxFunction\CheckboxFunctionService;
use JTL\Checkbox\CheckboxLanguage\CheckboxLanguageDomainObject;
use JTL\Checkbox\CheckboxLanguage\CheckboxLanguageService;
use JTL\Checkbox\CheckboxService;
use JTL\Checkbox\CheckboxValidationDomainObject;
use JTL\Customer\CustomerGroup;
use JTL\DB\DbInterface;
use JTL\Helpers\GeneralObject;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\Helpers\Typifier;
use JTL\Language\LanguageHelper;
use JTL\Link\Link;
use JTL\Mail\Mail\Mail;
use JTL\Mail\Mailer;
use JTL\Optin\Optin;
use JTL\Optin\OptinNewsletter;
use JTL\Optin\OptinRefData;
use JTL\Session\Frontend;
use Monolog\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

/**
 * Class CheckBox
 * @package JTL
 */
class CheckBox
{
    public const CHECKBOX_DOWNLOAD_ORDER_COMPLETE = 'RightOfWithdrawalOfDownloadItems';

    /**
     * @var int
     */
    public int $kCheckBox = 0;

    /**
     * @var int
     */
    public int $kLink = 0;

    /**
     * @var int
     */
    public int $kCheckBoxFunktion = 0;

    /**
     * @var string
     */
    public string $cName = '';

    /**
     * @var string
     */
    public string $cKundengruppe = '';

    /**
     * @var string
     */
    public string $cAnzeigeOrt = '';

    /**
     * @var int
     */
    public int $nAktiv = 0;

    /**
     * @var int
     */
    public int $nPflicht = 0;

    /**
     * @var int
     */
    public int $nLogging = 0;

    /**
     * @var int
     */
    public int $nSort = 0;

    /**
     * @var string
     */
    public string $dErstellt = '';

    /**
     * @var string
     */
    public string $dErstellt_DE = '';

    /**
     * @var array
     */
    public array $oCheckBoxSprache_arr = [];

    /**
     * @var stdClass|null
     */
    public ?stdClass $oCheckBoxFunktion = null;

    /**
     * @var array
     */
    public array $kKundengruppe_arr = [];

    /**
     * @var array
     */
    public array $kAnzeigeOrt_arr = [];

    /**
     * @var string|null
     */
    public ?string $cID = null;

    /**
     * @var string|null
     */
    public ?string $cLink = null;

    /**
     * @var Link|null
     */
    public ?Link $oLink = null;

    /**
     * @var string
     */
    public string $identifier;

    /**
     * @var bool|null
     */
    public ?bool $isActive = null;

    /**
     * @var string|null
     */
    public ?string $cLinkURL = null;

    /**
     * @var string|null
     */
    public ?string $cLinkURLFull = null;

    /**
     * @var string|null
     */
    public ?string $cBeschreibung = null;

    /**
     * @var string|null
     */
    public ?string $cErrormsg = null;

    /**
     * @var bool
     */
    protected bool $loggerAvailable = true;

    /**
     * @var int
     */
    public int $nInternal = 0;

    /**
     * @param int                          $id
     * @param DbInterface|null             $db
     * @param CheckboxService|null         $service
     * @param CheckboxLanguageService|null $languageService
     * @param CheckboxFunctionService|null $functionService
     * @param Logger|null                  $logService
     * @param JTLCacheInterface|null       $cache
     */
    public function __construct(
        int                                $id = 0,
        protected ?DbInterface             $db = null,
        protected ?CheckboxService         $service = null,
        protected ?CheckboxLanguageService $languageService = null,
        protected ?CheckboxFunctionService $functionService = null,
        protected ?Logger                  $logService = null,
        protected ?JTLCacheInterface       $cache = null
    ) {
        $this->dependencies();
        $this->oLink = new Link($this->db);
        $this->loadFromDB($id);
    }

    /**
     * @return void
     */
    private function dependencies(): void
    {
        $this->db              = $this->db ?? Shop::Container()->getDB();
        $this->service         = $this->service ?? new CheckboxService();
        $this->languageService = $this->languageService ?? new CheckboxLanguageService();
        $this->functionService = $this->functionService ?? new CheckboxFunctionService();
        try {
            $this->logService = $this->logService ?? Shop::Container()->getLogService();
            $this->cache      = $this->cache ?? Shop::Container()->getCache();
        } catch (Exception) {
            $this->loggerAvailable = false;
        }
    }

    /**
     * @param int $id
     * @return $this
     */
    private function loadFromDB(int $id): self
    {
        if ($id <= 0) {
            return $this;
        }
        $cacheID = 'chkbx_' . $id;
        if (($checkbox = $this->cache->get($cacheID)) !== false) {
            foreach (\array_keys(\get_object_vars($checkbox)) as $member) {
                if ($member === 'db') {
                    continue;
                }
                $this->$member = $checkbox->$member;
            }
            $this->loadLink();
            $this->checkAndUpdateFunctionIfNecessary($checkbox);

            return $this;
        }
        $checkbox = $this->service->get($id);

        if ($checkbox === null) {
            return $this;
        }
        $this->fillProperties($checkbox);
        $this->saveToCache($cacheID);

        return $this;
    }

    /**
     * @param string $cacheID
     * @return void
     */
    private function saveToCache(string $cacheID): void
    {
        $item = new stdClass();
        foreach (\get_object_vars($this) as $name => $value) {
            if (\is_object($this->$name)) {
                continue;
            }
            $item->$name = $value;
        }
        $this->cache->set($cacheID, $item, [\CACHING_GROUP_CORE, 'checkbox']);
    }

    /**
     * @return void
     */
    private function loadLink(): void
    {
        $this->oLink = new Link($this->db);
        if ($this->kLink > 0) {
            try {
                $this->oLink->load($this->kLink);
            } catch (InvalidArgumentException) {
                if ($this->loggerAvailable) {
                    $this->logService->error('Checkbox cannot link to link ID {id}', ['id' => $this->kLink]);
                }
            }
        } else {
            $this->cLink = 'kein interner Link';
        }
    }

    /**
     * @param int  $location
     * @param int  $customerGroupID
     * @param bool $active
     * @param bool $lang
     * @param bool $special
     * @param bool $logging
     * @return CheckBox[]
     */
    public function getCheckBoxFrontend(
        int  $location,
        int  $customerGroupID = 0,
        bool $active = false,
        bool $lang = false,
        bool $special = false,
        bool $logging = false
    ): array {
        if ($customerGroupID === 0) {
            if (isset($_SESSION['Kundengruppe']->kKundengruppe)) {
                $customerGroupID = Frontend::getCustomerGroup()->getID();
            } else {
                $customerGroupID = CustomerGroup::getDefaultGroupID();
            }
        }
        $validationData = new CheckboxValidationDomainObject(
            Typifier::intify($customerGroupID),
            Typifier::intify($location),
            Typifier::boolify($active),
            Typifier::boolify($logging),
            Typifier::boolify($lang),
            Typifier::boolify($special),
            false
        );

        return $this->service->getCheckBoxValidationData($validationData);
    }

    /**
     * @param int   $location
     * @param int   $customerGroupID
     * @param array $post
     * @param bool  $active
     * @return array
     */
    public function validateCheckBox(int $location, int $customerGroupID, array $post, bool $active = false): array
    {
        $checks = [];
        foreach ($this->getCheckBoxFrontend($location, $customerGroupID, $active) as $checkBox) {
            if ($checkBox->nPflicht === 1 && !isset($post[$checkBox->cID])) {
                $checks[$checkBox->cID] = 1;
            }
        }

        return $checks;
    }

    /**
     * @param int   $location
     * @param int   $customerGroupID
     * @param bool  $active
     * @param array $post
     * @param array $params
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \SmartyException
     */
    public function triggerSpecialFunction(
        int   $location,
        int   $customerGroupID,
        bool  $active,
        array $post,
        array $params = []
    ): self {
        $checkboxes = $this->getCheckBoxFrontend($location, $customerGroupID, $active, true, true);
        foreach ($checkboxes as $checkbox) {
            if (!isset($post[$checkbox->cID])) {
                continue;
            }
            if ($checkbox->oCheckBoxFunktion->pluginID > 0) {
                $params['oCheckBox'] = $checkbox;
                \executeHook(\HOOK_CHECKBOX_CLASS_TRIGGERSPECIALFUNCTION, $params);
            } else {
                // Festdefinierte Shopfunktionen
                switch ($checkbox->oCheckBoxFunktion->identifier) {
                    case 'jtl_newsletter': // Newsletteranmeldung
                        $params['oKunde'] = GeneralObject::copyMembers($params['oKunde']);
                        $this->sfCheckBoxNewsletter($params['oKunde'], $location);
                        break;

                    case 'jtl_adminmail': // CheckBoxMail
                        $params['oKunde'] = GeneralObject::copyMembers($params['oKunde']);
                        $this->sfCheckBoxMailToAdmin($params['oKunde'], $checkbox, $location);
                        break;

                    default:
                        break;
                }
            }
        }

        return $this;
    }

    /**
     * @param int   $location
     * @param int   $customerGroupID
     * @param array $post
     * @param bool  $active
     * @return $this
     */
    public function checkLogging(int $location, int $customerGroupID, array $post, bool $active = false): self
    {
        $checkboxes = $this->getCheckBoxFrontend($location, $customerGroupID, $active, false, false, true);
        foreach ($checkboxes as $checkbox) {
            $checked          = $this->checkboxWasChecked($checkbox->cID, $post);
            $log              = new stdClass();
            $log->kCheckBox   = $checkbox->kCheckBox;
            $log->kBesucher   = (int)($_SESSION['oBesucher']->kBesucher ?? 0);
            $log->kBestellung = (int)($_SESSION['kBestellung'] ?? 0);
            $log->bChecked    = (int)$checked;
            $log->dErstellt   = 'NOW()';
            $this->db->insert('tcheckboxlogging', $log);
        }

        return $this;
    }

    /**
     * @param string $idx
     * @param array  $post
     * @return bool
     */
    private function checkboxWasChecked(string $idx, array $post): bool
    {
        $value = $post[$idx] ?? null;
        if ($value === null) {
            return false;
        }
        if ($value === 'on' || $value === 'Y' || $value === 'y') {
            $value = true;
        } elseif ($value === 'N' || $value === 'n' || $value === '') {
            $value = false;
        } else {
            $value = (bool)$value;
        }

        return $value;
    }

    /**
     * @param string $limitSQL
     * @param bool   $active
     * @return CheckBox[]
     * @deprecated since 5.1.0
     */
    public function getAllCheckBox(string $limitSQL = '', bool $active = false): array
    {
        \trigger_error(__METHOD__ . ' is deprecated. Use JTL\CheckBox\getAll() instead.', \E_USER_DEPRECATED);

        return $this->getAll($limitSQL, $active);
    }

    /**
     * @param string $limitSQL
     * @param bool   $active
     * @return CheckBox[]
     */
    public function getAll(string $limitSQL = '', bool $active = false): array
    {
        return $this->db->getCollection(
            'SELECT kCheckBox AS id
                FROM tcheckbox' . ($active ? ' WHERE nAktiv = 1' : '') . '
                ORDER BY nSort ' . $limitSQL
        )->map(function (stdClass $e): self {
            return new self((int)$e->id, $this->db);
        })->all();
    }

    /**
     * @param bool $active
     * @return int
     * @deprecated since 5.1.0
     */
    public function getAllCheckBoxCount(bool $active = false): int
    {
        \trigger_error(__METHOD__ . ' is deprecated. Use JTL\CheckBox\getTotalCount() instead.', \E_USER_DEPRECATED);

        return $this->getTotalCount($active);
    }

    /**
     * @param bool $active
     * @return int
     */
    public function getTotalCount(bool $active = false): int
    {
        return (int)$this->db->getSingleObject(
            'SELECT COUNT(*) AS cnt
                FROM tcheckbox' . ($active ? ' WHERE nAktiv = 1' : '')
        )->cnt;
    }

    /**
     * @param int[] $checkboxIDs
     * @return bool
     * @deprecated since 5.1.0
     */
    public function aktivateCheckBox(array $checkboxIDs): bool
    {
        \trigger_error(__METHOD__ . ' is deprecated. Use JTL\CheckBox\activate() instead.', \E_USER_DEPRECATED);

        return $this->activate($checkboxIDs);
    }

    /**
     * @param int[] $checkboxIDs
     * @return bool
     */
    public function activate(array $checkboxIDs): bool
    {
        $res = $this->service->activate($checkboxIDs);
        $this->cache->flushTags(['checkbox']);

        return $res;
    }

    /**
     * @param int[] $checkboxIDs
     * @return bool
     * @deprecated since 5.1.0
     */
    public function deaktivateCheckBox(array $checkboxIDs): bool
    {
        \trigger_error(__METHOD__ . ' is deprecated. Use JTL\CheckBox\deactivate() instead.', \E_USER_DEPRECATED);

        return $this->deactivate($checkboxIDs);
    }

    /**
     * @param int[] $checkboxIDs
     * @return bool
     */
    public function deactivate(array $checkboxIDs): bool
    {
        $res = $this->service->deactivate($checkboxIDs);
        $this->cache->flushTags(['checkbox']);

        return $res;
    }

    /**
     * @param int[] $checkboxIDs
     * @return bool
     * @deprecated since 5.1.0
     */
    public function deleteCheckBox(array $checkboxIDs): bool
    {
        \trigger_error(__METHOD__ . ' is deprecated. Use JTL\CheckBox\delete() instead.', \E_USER_DEPRECATED);

        return $this->delete($checkboxIDs);
    }

    /**
     * @param int[] $checkboxIDs
     * @return bool
     */
    public function delete(array $checkboxIDs): bool
    {
        $res = $this->service->deleteByIDs($checkboxIDs);
        $this->cache->flushTags(['checkbox']);

        return $res;
    }

    /**
     * @return stdClass[]
     */
    public function getCheckBoxFunctions(): array
    {
        return $this->db->getCollection(
            'SELECT *
                FROM tcheckboxfunktion
                ORDER BY cName'
        )->each(static function (stdClass $e): void {
            $e->kCheckBoxFunktion = (int)$e->kCheckBoxFunktion;
            $e->cName             = \__($e->cName);
        })->all();
    }

    public function getCheckboxDomainObject(array $post, array $languages): CheckboxDomainObject
    {
        return $this->service->getCheckBoxDomainObject($post, $languages);
    }

    /**
     * @param CheckboxDomainObject $checkboxDO
     * @return $this
     */
    public function save(CheckboxDomainObject $checkboxDO): self
    {
        $this->populateSelf($checkboxDO);
        if (\count($checkboxDO->getLanguages()) === 0) {
            return $this;
        }
        $this->insertDB(null, null, $checkboxDO);
        //we don't know, when "now()" took place, therefore we have to look up, what actually has been saved
        $savedCheckbox = $this->service->get($this->kCheckBox);
        if ($savedCheckbox instanceof CheckboxDomainObject) {
            $this->fillProperties($savedCheckbox);
            $this->cache->flushTags(['checkbox']);
            $this->saveToCache('chkbx_' . $this->kCheckBox);
        }

        return $this;
    }

    /**
     * @param CheckboxDomainObject $checkboxDO
     * @return void
     */
    private function populateSelf(CheckboxDomainObject $checkboxDO): void
    {
        foreach ($checkboxDO->toArrayMapped() as $property => $value) {
            if (\array_key_exists($property, \get_object_vars($this))) {
                $this->$property = \is_bool($value) ? (int)$value : $value;
            }
        }
        foreach ($checkboxDO->getLanguages() as $iso => $texts) {
            $langID = $this->getSprachKeyByISO($iso);

            $this->oCheckBoxSprache_arr[$langID] = [
                'kCheckBox'     => $checkboxDO->getID(),
                'kSprache'      => $langID,
                'cText'         => $texts['text'],
                'cBeschreibung' => $texts['descr'],
            ];
        }
    }

    /**
     * @param array|null                $texts
     * @param array|null                $descriptions
     * @param CheckboxDomainObject|null $checkboxDO
     * @return $this
     */
    public function insertDB(
        ?array                $texts = [],
        ?array                $descriptions = [],
        ?CheckboxDomainObject $checkboxDO = null
    ): self {
        if (!isset($checkboxDO)) {
            $languages  = $this->prepareLanguagesForDO($texts, $descriptions);
            $checkboxDO = $this->getCheckboxDomainObject((array)$this, $languages);
        }
        $checkboxID = $checkboxDO->getCheckboxID();
        //Since method used to do the update too
        if ($checkboxID > 0) {
            $this->kCheckBox = $checkboxID;

            return $this->updateDB($checkboxDO);
        }
        $checkboxID                 = $this->service->insert($checkboxDO);
        $this->kCheckBox            = $checkboxID;
        $this->oCheckBoxSprache_arr = $this->addLocalization($checkboxDO, $checkboxID);

        return $this;
    }

    /**
     * @param CheckboxDomainObject $checkboxDO
     * @return $this
     */
    public function updateDB(CheckboxDomainObject $checkboxDO): self
    {
        $this->service->update($checkboxDO);
        $this->oCheckBoxSprache_arr = $this->updateLocalization($checkboxDO);

        return $this;
    }

    /**
     * @param CheckboxDomainObject $checkboxDO
     * @param int                  $checkboxID
     * @return array
     */
    private function addLocalization(CheckboxDomainObject $checkboxDO, int $checkboxID = 0): array
    {
        $checkBoxLanguageArr = [];
        foreach ($checkboxDO->getLanguages() as $iso => $texts) {
            $checkboxLanguageDO = $this->prepareLocalizationObject($checkboxID, $iso, $texts);
            $this->languageService->update($checkboxLanguageDO);
            $checkBoxLanguageArr[$checkboxLanguageDO->getLanguageID()] = $checkboxLanguageDO->toObjectMapped();
        }

        return $checkBoxLanguageArr;
    }

    /**
     * @param CheckboxDomainObject $checkboxDO
     * @return void
     */
    private function updateLocalization(CheckboxDomainObject $checkboxDO): array
    {
        $this->dismissObsoleteLanguages($checkboxDO->getCheckboxID());

        $checkBoxLanguageArr = [];
        foreach ($checkboxDO->getLanguages() as $iso => $texts) {
            $checkboxLanguageDO = $this->prepareLocalizationObject($checkboxDO->getCheckboxID(), $iso, $texts);
            $this->languageService->update($checkboxLanguageDO);
            $checkBoxLanguageArr[$checkboxLanguageDO->getLanguageID()] = $checkboxLanguageDO->toObjectMapped();
        }

        return $checkBoxLanguageArr;
    }

    /**
     * @param int    $checkBoxID
     * @param string $iso
     * @param array  $texts
     * @return CheckboxLanguageDomainObject
     */
    private function prepareLocalizationObject(
        int    $checkBoxID,
        string $iso,
        array  $texts = []
    ): CheckboxLanguageDomainObject {
        $checkboxLanguageDO = new CheckboxLanguageDomainObject(
            Typifier::intify($checkBoxID),
            0,
            $this->getSprachKeyByISO($iso),
            $iso,
            Typifier::stringify($texts['text']),
            Typifier::stringify($texts['descr']),
        );

        return $checkboxLanguageDO;
    }

    /**
     * @param string $iso
     * @return int
     */
    private function getSprachKeyByISO(string $iso): int
    {
        return (int)(LanguageHelper::getLangIDFromIso($iso)->kSprache ?? 0);
    }

    /**
     * @param object $customer
     * @param int    $location
     * @return bool
     */
    private function sfCheckBoxNewsletter($customer, int $location): bool
    {
        if (!\is_object($customer)) {
            return false;
        }
        $refData = (new OptinRefData())
            ->setSalutation($customer->cAnrede ?? '')
            ->setFirstName($customer->cVorname ?? '')
            ->setLastName($customer->cNachname ?? '')
            ->setEmail($customer->cMail)
            ->setLanguageID(Shop::getLanguageID())
            ->setRealIP(Request::getRealIP());
        try {
            (new Optin(OptinNewsletter::class))
                ->getOptinInstance()
                ->createOptin($refData, $location)
                ->sendActivationMail();
        } catch (Exception) {
            if ($this->loggerAvailable) {
                $this->logService->error('Checkbox cannot link to link ID {id}', ['id' => $this->kLink]);
            }

            return false;
        }

        return true;
    }

    /**
     * @param object $customer
     * @param object $checkBox
     * @param int    $location
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \SmartyException
     */
    public function sfCheckBoxMailToAdmin($customer, $checkBox, int $location): bool
    {
        if (!isset($customer->cVorname, $customer->cNachname, $customer->cMail)) {
            return false;
        }
        $conf = Shop::getSettingSection(\CONF_EMAILS);
        if (!empty($conf['email_master_absender'])) {
            $data                = new stdClass();
            $data->oCheckBox     = $checkBox;
            $data->oKunde        = $customer;
            $data->tkunde        = $customer;
            $data->cAnzeigeOrt   = $this->mappeCheckBoxOrte($location);
            $data->mail          = new stdClass();
            $data->mail->toEmail = $conf['email_master_absender'];
            $data->mail->toName  = $conf['email_master_absender_name']
                ?? $conf['email_master_absender'];
            /** @var Mailer $mailer */
            $mailer = Shop::Container()->get(Mailer::class);
            $mail   = new Mail();
            return $mailer->send($mail->createFromTemplateID(\MAILTEMPLATE_CHECKBOX_SHOPBETREIBER, $data));
        }

        return false;
    }

    /**
     * @param int $location
     * @return string
     */
    public function mappeCheckBoxOrte(int $location): string
    {
        return self::gibCheckBoxAnzeigeOrte()[$location] ?? '';
    }

    /**
     * @return array
     */
    public static function gibCheckBoxAnzeigeOrte(): array
    {
        Shop::Container()->getGetText()->loadAdminLocale('pages/checkbox');

        return [
            \CHECKBOX_ORT_REGISTRIERUNG        => \__('checkboxPositionRegistration'),
            \CHECKBOX_ORT_BESTELLABSCHLUSS     => \__('checkboxPositionOrderFinal'),
            \CHECKBOX_ORT_NEWSLETTERANMELDUNG  => \__('checkboxPositionNewsletterRegistration'),
            \CHECKBOX_ORT_KUNDENDATENEDITIEREN => \__('checkboxPositionEditCustomerData'),
            \CHECKBOX_ORT_KONTAKT              => \__('checkboxPositionContactForm'),
            \CHECKBOX_ORT_FRAGE_ZUM_PRODUKT    => \__('checkboxPositionProductQuestion'),
            \CHECKBOX_ORT_FRAGE_VERFUEGBARKEIT => \__('checkboxPositionAvailabilityNotification')
        ];
    }

    /**
     * @return Link
     */
    public function getLink(): Link
    {
        return $this->oLink;
    }

    /**
     * @return CheckboxDataTableObject
     */
    protected function getCheckBoxDataTableObject(): CheckboxDataTableObject
    {
        $dataObject = new CheckboxDataTableObject();
        $dataObject->hydrate(\get_object_vars($this));

        return $dataObject;
    }

    /**
     * @param int $kCheckBox
     * @return void
     */
    public function dismissObsoleteLanguages(int $kCheckBox): void
    {
        $this->db->queryPrepared(
            'DELETE FROM tcheckboxsprache 
                WHERE kSprache NOT IN (SELECT kSprache FROM tsprache) AND kCheckBox = :kCheckBox',
            ['kCheckBox' => $kCheckBox]
        );
    }

    /**
     * @param array|null              $texts
     * @param CheckboxDataTableObject $checkboxDTO
     * @param array|null              $descriptions
     * @return void
     */
    protected function prepareLanguagesForDO(?array $texts, ?array $descriptions): array
    {
        $languages = [];
        foreach ($texts as $iso => $language) {
            $languages[$iso] = [
                'text'  => $language,
                'descr' => $descriptions[$iso] ?? ''
            ];
        }

        return $languages;
    }

    /**
     * @param CheckboxDomainObject $checkbox
     * @return void
     */
    public function fillProperties(CheckboxDomainObject $checkbox): void
    {
        $this->kCheckBox         = $checkbox->getCheckboxID();
        $this->kLink             = $checkbox->getLinkID();
        $this->kCheckBoxFunktion = $checkbox->getCheckboxFunctionID();
        $this->cName             = $checkbox->getName();
        $this->cKundengruppe     = $checkbox->getCustomerGroupsSelected();
        $this->cAnzeigeOrt       = $checkbox->getDisplayAt();
        $this->nAktiv            = (int)$checkbox->isActive();
        $this->nPflicht          = (int)$checkbox->isMandatory();
        $this->nLogging          = (int)$checkbox->isLogging();
        $this->nSort             = $checkbox->getSort();
        $this->cID               = 'CheckBox_' . $this->kCheckBox;
        $this->dErstellt         = $checkbox->getCreated();
        $this->dErstellt_DE      = $checkbox->getCreatedDE();
        $this->kKundengruppe_arr = Text::parseSSKint($checkbox->getCustomerGroupsSelected());
        $this->kAnzeigeOrt_arr   = Text::parseSSKint($checkbox->getDisplayAt());
        $this->nInternal         = (int)$checkbox->getInternal();
        if ($this->kCheckBoxFunktion > 0) {
            $this->checkAndUpdateFunctionIfNecessary($checkbox);
        }
        $this->loadLink();
        if ($this->oCheckBoxSprache_arr === []) {
            $localized = $this->languageService->getList(['kCheckBox' => $this->kCheckBox]);
            foreach ($localized as $translation) {
                $this->oCheckBoxSprache_arr[$translation->getLanguageID()] = $translation->toObjectMapped(true);
            }
        }
    }

    /**
     * @param CheckboxDomainObject|stdClass $checkbox
     * @return void
     */
    public function checkAndUpdateFunctionIfNecessary(CheckboxDomainObject|stdClass $checkbox): void
    {
        $functionData = $this->functionService->get($this->kCheckBoxFunktion);
        if ($functionData !== null) {
            if (Shop::isAdmin()) {
                Shop::Container()->getGetText()->loadAdminLocale('pages/checkbox');
                $name = (\__($functionData->cName));
            } else {
                $name = $functionData->cName;
            }
            // Falls kCheckBoxFunktion gesetzt war aber diese Funktion nicht mehr existiert (deinstallation vom Plugin)
            // wird kCheckBoxFunktion auf 0 gesetzt
            $func = (new CheckboxFunctionDomainObject(
                Typifier::intifyOrNull($functionData->kPlugin),
                Typifier::intifyOrNull($functionData->kCheckBoxFunktion),
                Typifier::stringify($name),
                Typifier::stringify($functionData->cID)
            ));

            $this->oCheckBoxFunktion = $func->toObject();
        } else {
            $data = (new CheckboxFunctionDomainObject(0, 0, '', ''));

            $this->service->update($data);
        }
    }
}
