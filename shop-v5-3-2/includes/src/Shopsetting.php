<?php declare(strict_types=1);

namespace JTL;

use ArrayAccess;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Helpers\Text;
use function Functional\reindex;

/**
 * Class Shopsetting
 * @package JTL
 */
final class Shopsetting implements ArrayAccess
{
    /**
     * @var Shopsetting|null
     */
    private static ?self $instance = null;

    /**
     * @var array
     */
    private array $container = [];

    /**
     * @var array|null
     */
    private ?array $allSettings = null;

    /**
     * @var array
     */
    private static array $mapping = [
        \CONF_GLOBAL              => 'global',
        \CONF_STARTSEITE          => 'startseite',
        \CONF_EMAILS              => 'emails',
        \CONF_ARTIKELUEBERSICHT   => 'artikeluebersicht',
        \CONF_ARTIKELDETAILS      => 'artikeldetails',
        \CONF_KUNDEN              => 'kunden',
        \CONF_LOGO                => 'logo',
        \CONF_KAUFABWICKLUNG      => 'kaufabwicklung',
        \CONF_BOXEN               => 'boxen',
        \CONF_BILDER              => 'bilder',
        \CONF_SONSTIGES           => 'sonstiges',
        \CONF_ZAHLUNGSARTEN       => 'zahlungsarten',
        \CONF_PLUGINZAHLUNGSARTEN => 'pluginzahlungsarten',
        \CONF_KONTAKTFORMULAR     => 'kontakt',
        \CONF_SHOPINFO            => 'shopinfo',
        \CONF_RSS                 => 'rss',
        \CONF_VERGLEICHSLISTE     => 'vergleichsliste',
        \CONF_PREISVERLAUF        => 'preisverlauf',
        \CONF_BEWERTUNG           => 'bewertung',
        \CONF_NEWSLETTER          => 'newsletter',
        \CONF_KUNDENFELD          => 'kundenfeld',
        \CONF_NAVIGATIONSFILTER   => 'navigationsfilter',
        \CONF_EMAILBLACKLIST      => 'emailblacklist',
        \CONF_METAANGABEN         => 'metaangaben',
        \CONF_NEWS                => 'news',
        \CONF_SITEMAP             => 'sitemap',
        \CONF_SUCHSPECIAL         => 'suchspecials',
        \CONF_TEMPLATE            => 'template',
        \CONF_AUSWAHLASSISTENT    => 'auswahlassistent',
        \CONF_CRON                => 'cron',
        \CONF_FS                  => 'fs',
        \CONF_CACHING             => 'caching',
        \CONF_CONSENTMANAGER      => 'consentmanager',
        \CONF_BRANDING            => 'branding'
    ];

    /**
     * @param DbInterface       $db
     * @param JTLCacheInterface $cache
     */
    private function __construct(private readonly DbInterface $db, private readonly JTLCacheInterface $cache)
    {
        self::$instance = $this;
    }

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     * @param DbInterface|null       $db
     * @param JTLCacheInterface|null $cache
     * @return self
     */
    public static function getInstance(?DbInterface $db = null, JTLCacheInterface $cache = null): self
    {
        return self::$instance ?? new self($db ?? Shop::Container()->getDB(), $cache ?? Shop::Container()->getCache());
    }

    /**
     * for rare cases when options are modified and directly re-assigned to smarty
     * do not call this function otherwise.
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->container = [];

        return $this;
    }

    /**
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * @param int   $sectionID
     * @param array $value
     */
    public function overrideSection(int $sectionID, array $value): void
    {
        $mapping = self::mapSettingName($sectionID);
        if ($mapping !== null) {
            $this->container[$mapping]   = $value;
            $this->allSettings[$mapping] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (isset($this->container[$offset])) {
            return $this->container[$offset];
        }
        $section = self::mapSettingName(null, $offset);
        $cacheID = 'setting_' . $section;
        if ($section === false || $section === null) {
            return null;
        }
        if ($section === \CONF_TEMPLATE) {
            $settings = $this->cache->get(
                $cacheID,
                function ($cache, $id, &$content, &$tags): bool {
                    $content = $this->getTemplateConfig();
                    $tags    = [\CACHING_GROUP_TEMPLATE, \CACHING_GROUP_OPTION];

                    return true;
                }
            );
            if (\is_array($settings)) {
                foreach ($settings as $templateSection => $templateSetting) {
                    $this->container[$offset][$templateSection] = $templateSetting;
                }
            }
        } elseif ($section === \CONF_BRANDING) {
            return $this->cache->get(
                $cacheID,
                function ($cache, $id, &$content, &$tags): bool {
                    $content = $this->getBrandingConfig();
                    $tags    = [\CACHING_GROUP_OPTION];

                    return true;
                }
            );
        } else {
            $settings = $this->cache->get(
                $cacheID,
                function ($cache, $id, &$content, &$tags) use ($section): bool {
                    $content = $this->getSectionData($section);
                    $tags    = [\CACHING_GROUP_OPTION];

                    return true;
                }
            );
            if (\count($settings) > 0) {
                $this->addContainerData($offset, $settings);
            }
        }

        return $this->container[$offset] ?? null;
    }

    /**
     * @param string $offset
     * @param array  $settings
     */
    private function addContainerData(string $offset, array $settings): void
    {
        $this->container[$offset] = [];
        foreach ($settings as $setting) {
            if ($setting->type === 'listbox') {
                if (!isset($this->container[$offset][$setting->cName])) {
                    $this->container[$offset][$setting->cName] = [];
                }
                $this->container[$offset][$setting->cName][] = $setting->cWert;
            } elseif ($setting->type === 'number') {
                $this->container[$offset][$setting->cName] = (int)$setting->cWert;
            } else {
                $this->container[$offset][$setting->cName] = $setting->cWert;
            }
        }
    }

    /**
     * @param int $section
     * @return array
     */
    private function getSectionData(int $section): array
    {
        if ($section === \CONF_PLUGINZAHLUNGSARTEN) {
            return $this->db->getObjects(
                "SELECT cName, cWert, '' AS type
                     FROM tplugineinstellungen
                     WHERE cName LIKE '%_min%' 
                        OR cName LIKE '%_max'"
            );
        }

        return $this->db->getObjects(
            'SELECT teinstellungen.cName, teinstellungen.cWert, teinstellungenconf.cInputTyp AS type
                FROM teinstellungen
                LEFT JOIN teinstellungenconf
                    ON teinstellungenconf.cWertName = teinstellungen.cName
                    AND teinstellungenconf.kEinstellungenSektion = teinstellungen.kEinstellungenSektion
                WHERE teinstellungen.kEinstellungenSektion = :section',
            ['section' => $section]
        );
    }

    /**
     * @param int|array $sections
     * @return array
     */
    public function getSettings(int|array $sections): array
    {
        $ret = [];
        foreach ((array)$sections as $section) {
            $mapping = self::mapSettingName($section);
            if ($mapping !== null) {
                $ret[$mapping] = $this[$mapping];
            }
        }

        return $ret;
    }

    /**
     * @param int    $sectionID
     * @param string $option
     * @return mixed
     */
    public function getValue(int $sectionID, string $option): mixed
    {
        $section = $this->getSection($sectionID);

        return $section[$option] ?? null;
    }

    /**
     * @param int $sectionID
     * @return array|null
     */
    public function getSection(int $sectionID): ?array
    {
        $settings = $this->getSettings([$sectionID]);

        return $settings[self::mapSettingName($sectionID)] ?? null;
    }

    /**
     * @param null|int    $section
     * @param null|string $name
     * @return mixed|null
     */
    public static function mapSettingName(?int $section = null, ?string $name = null): mixed
    {
        if ($section === null && $name === null) {
            return false;
        }
        if ($section !== null && isset(self::$mapping[$section])) {
            return self::$mapping[$section];
        }
        if ($name !== null && ($key = \array_search($name, self::$mapping, true)) !== false) {
            return $key;
        }

        return null;
    }

    /**
     * @return array
     */
    private function getBrandingConfig(): array
    {
        $data = $this->db->getObjects(
            'SELECT tbrandingeinstellung.*, tbranding.kBranding AS id, tbranding.cBildKategorie AS type, 
            tbrandingeinstellung.cPosition AS position, tbrandingeinstellung.cBrandingBild AS path,
            tbrandingeinstellung.dTransparenz AS transparency, tbrandingeinstellung.dGroesse AS size
                FROM tbrandingeinstellung
                INNER JOIN tbranding 
                    ON tbrandingeinstellung.kBranding = tbranding.kBranding
                WHERE tbrandingeinstellung.nAktiv = 1'
        );
        foreach ($data as $item) {
            $item->size         = (int)$item->size;
            $item->transparency = (int)$item->transparency;
            $item->path         = \PFAD_ROOT . \PFAD_BRANDINGBILDER . $item->path;
            $item->imagesizes   = Text::parseSSK($item->imagesizes ?? '');
            unset(
                $item->kBrandingEinstellung,
                $item->kBranding,
                $item->nAktiv,
                $item->cPosition,
                $item->cBrandingBild,
                $item->dTransparenz,
                $item->dRandabstand,
                $item->dGroesse,
            );
        }

        return reindex($data, static function ($e) {
            return $e->type;
        });
    }

    /**
     * @return array
     */
    private function getTemplateConfig(): array
    {
        $data     = $this->db->getObjects(
            "SELECT cSektion AS sec, cWert AS val, cName AS name 
                FROM ttemplateeinstellungen 
                WHERE cTemplate = (SELECT cTemplate FROM ttemplate WHERE eTyp = 'standard')"
        );
        $settings = [];
        foreach ($data as $setting) {
            if (!isset($settings[$setting->sec])) {
                $settings[$setting->sec] = [];
            }
            $settings[$setting->sec][$setting->name] = $setting->val;
        }

        return $settings;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        if ($this->allSettings !== null) {
            return $this->allSettings;
        }
        $result   = [];
        $settings = $this->db->getArrays(
            'SELECT teinstellungen.kEinstellungenSektion, teinstellungen.cName, teinstellungen.cWert,
                teinstellungenconf.cInputTyp AS type
                FROM teinstellungen
                LEFT JOIN teinstellungenconf
                    ON teinstellungenconf.cWertName = teinstellungen.cName
                    AND teinstellungenconf.kEinstellungenSektion = teinstellungen.kEinstellungenSektion
                ORDER BY kEinstellungenSektion'
        );
        foreach (self::$mapping as $mappingID => $sectionName) {
            foreach ($settings as $setting) {
                $sectionID = (int)$setting['kEinstellungenSektion'];
                if ($sectionID === $mappingID) {
                    if (!isset($result[$sectionName])) {
                        $result[$sectionName] = [];
                    }
                    if ($setting['type'] === 'listbox') {
                        if (!isset($result[$sectionName][$setting['cName']])) {
                            $result[$sectionName][$setting['cName']] = [];
                        }
                        $result[$sectionName][$setting['cName']][] = $setting['cWert'];
                    } elseif ($setting['type'] === 'number') {
                        $result[$sectionName][$setting['cName']] = (int)$setting['cWert'];
                    } else {
                        $result[$sectionName][$setting['cName']] = $setting['cWert'];
                    }
                }
            }
        }
        $result['template'] = $this->getTemplateConfig();
        $result['branding'] = $this->getBrandingConfig();
        $this->allSettings  = $result;

        return $result;
    }

    /**
     * preload the _container variable with one single sql statement or one single cache call
     * this is being called after successful cache initialisation in class.JTL-Shop.JTLCache.php
     *
     * @return array
     */
    public function preLoad(): array
    {
        $cacheID           = 'settings_all_preload';
        $result            = $this->cache->get(
            $cacheID,
            function ($cache, $id, &$content, &$tags): bool {
                $content = $this->getAll();
                $tags    = [\CACHING_GROUP_TEMPLATE, \CACHING_GROUP_OPTION, \CACHING_GROUP_CORE];

                return true;
            }
        );
        $this->container   = $result;
        $this->allSettings = $result;

        return $result;
    }
}
