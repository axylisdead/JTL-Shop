<?php declare(strict_types=1);

namespace JTL\Consent;

use JTL\Model\DataModelInterface;
use JTL\Shop;

/**
 * Class Item
 * @package JTL\Consent
 */
class Item implements ItemInterface
{
    /**
     * @var int
     */
    private int $id = 0;

    /**
     * @var int
     */
    private int $pluginID = 0;

    /**
     * @var string
     */
    private string $itemID = '';

    /**
     * @var string[]
     */
    private array $name = [];

    /**
     * @var string[]
     */
    private array $description = [];

    /**
     * @var string[]
     */
    private array $purpose = [];

    /**
     * @var string[]
     */
    private array $company = [];

    /**
     * @var string[]
     */
    private array $privacyPolicy = [];

    /**
     * @var int
     */
    private int $currentLanguageID;

    /**
     * @var bool
     */
    private bool $active = false;

    /**
     * Item constructor.
     * @param int|null $currentLanguageID
     */
    public function __construct(int $currentLanguageID = null)
    {
        $this->currentLanguageID = $currentLanguageID ?? Shop::getLanguageID();
    }

    /**
     * @param ConsentModel|DataModelInterface $model
     * @return $this
     */
    public function loadFromModel(DataModelInterface $model): self
    {
        $this->setID($model->getId());
        $this->setItemID($model->getItemID());
        $this->setCompany($model->getCompany());
        $this->setPluginID($model->getPluginID());
        $this->setActive($model->getActive() === 1);
        foreach ($model->getLocalization() as $localization) {
            /** @var ConsentLocalizationModel $localization */
            $langID = $localization->getLanguageID();
            $this->setName($localization->getName(), $langID);
            $this->setPrivacyPolicy($localization->getPrivacyPolicy(), $langID);
            $this->setDescription($localization->getDescription(), $langID);
            $this->setPurpose($localization->getPurpose(), $langID);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setID($id): void
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getName(int $idx = null): string
    {
        return $this->name[$idx ?? $this->currentLanguageID] ?? '';
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name, int $idx = null): void
    {
        $this->name[$idx ?? $this->currentLanguageID] = $name;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(int $idx = null): string
    {
        return $this->description[$idx ?? $this->currentLanguageID] ?? '';
    }

    /**
     * @inheritdoc
     */
    public function setDescription(string $description, int $idx = null): void
    {
        $this->description[$idx ?? $this->currentLanguageID] = $description;
    }

    /**
     * @inheritdoc
     */
    public function getPurpose(int $idx = null): string
    {
        return $this->purpose[$idx ?? $this->currentLanguageID] ?? '';
    }

    /**
     * @inheritdoc
     */
    public function setPurpose(string $purpose, int $idx = null): void
    {
        $this->purpose[$idx ?? $this->currentLanguageID] = $purpose;
    }

    /**
     * @inheritdoc
     */
    public function getCompany(int $idx = null): string
    {
        return $this->company[$idx ?? $this->currentLanguageID] ?? '';
    }

    /**
     * @inheritdoc
     */
    public function setCompany(string $company, int $idx = null): void
    {
        $this->company[$idx ?? $this->currentLanguageID] = $company;
    }

    /**
     * @inheritdoc
     */
    public function getPrivacyPolicy(int $idx = null): string
    {
        return $this->privacyPolicy[$idx ?? $this->currentLanguageID] ?? '';
    }

    /**
     * @inheritdoc
     */
    public function setPrivacyPolicy(string $tos, int $idx = null): void
    {
        $this->privacyPolicy[$idx ?? $this->currentLanguageID] = $tos;
    }

    /**
     * @inheritdoc
     */
    public function hasMoreInfo(): bool
    {
        return !empty($this->getPurpose()) || !empty($this->getCompany()) || !empty($this->getPrivacyPolicy());
    }

    /**
     * @inheritdoc
     */
    public function getCurrentLanguageID(): int
    {
        return $this->currentLanguageID;
    }

    /**
     * @inheritdoc
     */
    public function setCurrentLanguageID(int $currentLanguageID): void
    {
        $this->currentLanguageID = $currentLanguageID;
    }

    /**
     * @return string
     */
    public function getItemID(): string
    {
        return $this->itemID;
    }

    /**
     * @param string $itemID
     */
    public function setItemID(string $itemID): void
    {
        $this->itemID = $itemID;
    }

    /**
     * @return int
     */
    public function getPluginID(): int
    {
        return $this->pluginID;
    }

    /**
     * @param int $pluginID
     */
    public function setPluginID(int $pluginID): void
    {
        $this->pluginID = $pluginID;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
