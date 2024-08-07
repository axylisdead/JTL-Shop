<?php declare(strict_types=1);

namespace JTL\dbeS\Sync;

use JTL\dbeS\Starter;
use JTL\Media\Image;
use JTL\Media\IMedia;
use JTL\Media\Media;
use JTL\Shop;
use stdClass;
use function Functional\map;
use function Functional\reindex;

/**
 * Class Images
 * @package JTL\dbeS\Sync
 */
final class Images extends AbstractSync
{
    /**
     * @var array
     */
    private array $config;

    /**
     * @var array
     */
    private array $brandingConfig;

    /**
     * @var string
     */
    private string $unzipPath;

    /**
     * @param Starter $starter
     * @return mixed|null
     */
    public function handle(Starter $starter)
    {
        $this->brandingConfig = $this->initBrandingConfig();
        $this->config         = Shop::getSettingSection(\CONF_BILDER);
        $this->db->query('START TRANSACTION');
        foreach ($starter->getXML() as $item) {
            [$file, $xml] = [\key($item), \reset($item)];
            switch (\pathinfo($file, \PATHINFO_BASENAME)) {
                case 'bilder_ka.xml':
                case 'bilder_a.xml':
                case 'bilder_k.xml':
                case 'bilder_v.xml':
                case 'bilder_m.xml':
                case 'bilder_mw.xml':
                case 'bilder_h.xml':
                    $this->handleInserts($xml, $starter->getUnzipPath());
                    break;

                case 'del_bilder_ka.xml':
                case 'del_bilder_a.xml':
                case 'del_bilder_k.xml':
                case 'del_bilder_v.xml':
                case 'del_bilder_m.xml':
                case 'del_bilder_mw.xml':
                case 'del_bilder_h.xml':
                    $this->handleDeletes($xml);
                    break;
                default:
                    break;
            }
        }
        $this->db->query('COMMIT');

        return null;
    }

    /**
     * @return array
     */
    private function initBrandingConfig(): array
    {
        return map(
            reindex(
                $this->db->getObjects('SELECT * FROM tbranding'),
                static function ($e) {
                    return $e->cBildKategorie;
                }
            ),
            function ($e) {
                $e->config = $this->db->select(
                    'tbrandingeinstellung',
                    'kBranding',
                    (int)$e->kBranding
                );
                return $e;
            }
        );
    }

    /**
     * @param string $type
     * @return stdClass|null
     */
    private function getBrandingConfig(string $type): ?stdClass
    {
        return $this->brandingConfig[$type]->config ?? null;
    }

    /**
     * @param array  $xml
     * @param string $unzipPath
     */
    private function handleInserts(array $xml, string $unzipPath): void
    {
        if (!\is_array($xml['bilder'])) {
            return;
        }
        $categoryImages     = $this->mapper->mapArray($xml['bilder'], 'tkategoriepict', 'mKategoriePict');
        $propertyImages     = $this->mapper->mapArray($xml['bilder'], 'teigenschaftwertpict', 'mEigenschaftWertPict');
        $manufacturerImages = $this->mapper->mapArray($xml['bilder'], 'therstellerbild', 'mEigenschaftWertPict');
        $charImages         = $this->mapper->mapArray($xml['bilder'], 'tMerkmalbild', 'mEigenschaftWertPict');
        $charValImages      = $this->mapper->mapArray($xml['bilder'], 'tmerkmalwertbild', 'mEigenschaftWertPict');
        $configGroupImages  = $this->mapper->mapArray($xml['bilder'], 'tkonfiggruppebild', 'mKonfiggruppePict');

        \executeHook(\HOOK_BILDER_XML_BEARBEITE, [
            'Pfad'             => $unzipPath,
            'Kategorie'        => &$categoryImages,
            'Eigenschaftswert' => &$propertyImages,
            'Hersteller'       => &$manufacturerImages,
            'Merkmalwert'      => &$charValImages,
            'Merkmal'          => &$charImages,
            'Konfiggruppe'     => &$configGroupImages
        ]);
        $this->unzipPath = $unzipPath;

        $this->handleCategoryImages($categoryImages);
        $this->handlePropertyImages($propertyImages);
        $this->handleManufacturerImages($manufacturerImages);
        $this->handleCharacteristicImages($charImages);
        $this->handleCharacteristicValueImages($charValImages);
        $this->handleConfigGroupImages($configGroupImages);
        if (\count($charImages) > 0 || \count($charValImages) > 0) {
            $this->cache->flushTags([\CACHING_GROUP_ATTRIBUTE, \CACHING_GROUP_FILTER_CHARACTERISTIC]);
        }

        \executeHook(\HOOK_BILDER_XML_BEARBEITE_ENDE, [
            'Kategorie'        => &$categoryImages,
            'Eigenschaftswert' => &$propertyImages,
            'Hersteller'       => &$manufacturerImages,
            'Merkmalwert'      => &$charValImages,
            'Merkmal'          => &$charImages,
            'Konfiggruppe'     => &$configGroupImages
        ]);
    }

    /**
     * @param array $images
     */
    private function handleConfigGroupImages(array $images): void
    {
        $flushIDs = [];
        foreach ($images as $image) {
            if (empty($image->cPfad) || empty($image->kKonfiggruppe)) {
                continue;
            }
            $item                = new stdClass();
            $item->cBildPfad     = $image->cPfad;
            $item->kKonfiggruppe = (int)$image->kKonfiggruppe;
            $original            = $this->unzipPath . $item->cBildPfad;
            $extension           = $this->getExtension($original);
            $flushIDs[]          = $item->kKonfiggruppe;
            if (!$extension) {
                $this->logger->error(
                    'Bildformat des Konfiggruppenbildes konnte nicht ermittelt werden. Datei ' .
                    $original . ' keine Bilddatei?'
                );
                continue;
            }
            $item->cBildPfad = $this->getNewFilename($item->kKonfiggruppe . '.' . $extension);
            \copy($original, \PFAD_ROOT . \STORAGE_CONFIGGROUPS . $item->cBildPfad);
            $this->db->update(
                'tkonfiggruppe',
                'kKonfiggruppe',
                $item->kKonfiggruppe,
                (object)['cBildPfad' => $item->cBildPfad]
            );
            \unlink($original);
        }
        $this->clearImageCache(Image::TYPE_CONFIGGROUP, $flushIDs);
    }

    /**
     * @param array $images
     */
    private function handleCharacteristicValueImages(array $images): void
    {
        $flushIDs = [];
        foreach ($images as $image) {
            $image->kMerkmalWert = (int)$image->kMerkmalWert;
            if (empty($image->cPfad) || $image->kMerkmalWert <= 0) {
                continue;
            }
            $original   = $this->unzipPath . $image->cPfad;
            $extension  = $this->getExtension($original);
            $flushIDs[] = $image->kMerkmalWert;
            if (!$extension) {
                $this->logger->error(
                    'Bildformat des Merkmalwertbildes konnte nicht ermittelt werden. Datei ' .
                    $original . ' keine Bilddatei?'
                );
                continue;
            }
            $image->cPfad = $this->getCharacteristicValueImageName($image, $extension);
            \copy($original, \PFAD_ROOT . \STORAGE_CHARACTERISTIC_VALUES . $image->cPfad);
            $this->db->update(
                'tmerkmalwert',
                'kMerkmalWert',
                $image->kMerkmalWert,
                (object)['cBildpfad' => $image->cPfad]
            );
            $charValImage               = new stdClass();
            $charValImage->kMerkmalWert = $image->kMerkmalWert;
            $charValImage->cBildpfad    = $image->cPfad;
            $this->upsert('tmerkmalwertbild', [$charValImage], 'kMerkmalWert');
            \unlink($original);
        }
        $this->clearImageCache(Image::TYPE_CHARACTERISTIC_VALUE, $flushIDs);
    }

    /**
     * @param array $images
     */
    private function handleCharacteristicImages(array $images): void
    {
        $flushIDs = [];
        foreach ($images as $image) {
            if (empty($image->cPfad) || empty($image->kMerkmal)) {
                continue;
            }
            $image->kMerkmal = (int)$image->kMerkmal;
            $original        = $this->unzipPath . $image->cPfad;
            $extension       = $this->getExtension($original);
            $flushIDs[]      = $image->kMerkmal;
            if (!$extension) {
                $this->logger->error(
                    'Bildformat des Merkmalbildes konnte nicht ermittelt werden. Datei ' .
                    $original . ' keine Bilddatei?'
                );
                continue;
            }
            $image->cPfad = $this->getCharacteristicImageName($image, $extension);
            \copy($original, \PFAD_ROOT . \STORAGE_CHARACTERISTICS . $image->cPfad);
            $this->db->update(
                'tmerkmal',
                'kMerkmal',
                $image->kMerkmal,
                (object)['cBildpfad' => $image->cPfad]
            );
            \unlink($original);
        }
        $this->clearImageCache(Image::TYPE_CHARACTERISTIC, $flushIDs);
    }

    /**
     * @param array $images
     */
    private function handleManufacturerImages(array $images): void
    {
        $flushIDs = [];
        foreach ($images as $image) {
            if (empty($image->cPfad) || empty($image->kHersteller)) {
                continue;
            }
            $image->kHersteller = (int)$image->kHersteller;
            $original           = $this->unzipPath . $image->cPfad;
            $extension          = $this->getExtension($original);
            $flushIDs[]         = $image->kHersteller;
            if (!$extension) {
                $this->logger->error(
                    'Bildformat des Herstellerbildes konnte nicht ermittelt werden. Datei ' .
                    $original . ' keine Bilddatei?'
                );
                continue;
            }
            $image->cPfad = $this->getManufacturerImageName($image, $extension);
            \copy($original, \PFAD_ROOT . \STORAGE_MANUFACTURERS . $image->cPfad);
            $this->db->update(
                'thersteller',
                'kHersteller',
                $image->kHersteller,
                (object)['cBildpfad' => $image->cPfad]
            );
            $cacheTags = [];
            foreach ($this->db->selectAll(
                'tartikel',
                'kHersteller',
                $image->kHersteller,
                'kArtikel'
            ) as $product) {
                $cacheTags[] = \CACHING_GROUP_ARTICLE . '_' . $product->kArtikel;
            }
            $this->cache->flushTags($cacheTags);
            \unlink($original);
        }
        $this->clearImageCache(Image::TYPE_MANUFACTURER, $flushIDs);
    }

    /**
     * @param array $images
     */
    private function handlePropertyImages(array $images): void
    {
        $flushIDs = [];
        foreach ($images as $image) {
            if (empty($image->cPfad)) {
                continue;
            }
            $image->kEigenschaftWert = (int)($image->kEigenschaftWert ?? 0);
            $flushIDs[]              = $image->kEigenschaftWert;
            $original                = $this->unzipPath . $image->cPfad;
            $extension               = $this->getExtension($original);
            if (!$extension) {
                $this->logger->error(
                    'Bildformat des Eigenschaftwertbildes konnte nicht ermittelt werden. Datei ' .
                    $original . ' keine Bilddatei?'
                );
                continue;
            }
            $image->cPfad = $this->getPropertiesImageName($image, $extension);
            $image->cPfad = $this->getNewFilename($image->cPfad);
            \copy($original, \PFAD_ROOT . \STORAGE_VARIATIONS . $image->cPfad);
            $this->upsert('teigenschaftwertpict', [$image], 'kEigenschaftWert');
            \unlink($original);
        }
        $this->clearImageCache(Image::TYPE_VARIATION, $flushIDs);
    }

    /**
     * @param array $images
     */
    private function handleCategoryImages(array $images): void
    {
        $flushIDs = [];
        foreach ($images as $image) {
            if (empty($image->cPfad)) {
                continue;
            }
            $flushIDs[] = (int)$image->kKategorie;
            $original   = $this->unzipPath . $image->cPfad;
            $extension  = $this->getExtension($original);
            if (!$extension) {
                $this->logger->error(
                    'Bildformat des Kategoriebildes konnte nicht ermittelt werden. Datei ' .
                    $original . ' keine Bilddatei?'
                );
                continue;
            }
            $image->cPfad = $this->getCategoryImageName($image, $extension);
            \copy($original, \PFAD_ROOT . \STORAGE_CATEGORIES . $image->cPfad);
            $this->upsert('tkategoriepict', [$image], 'kKategorie');
            \unlink($original);
        }
        $this->clearImageCache(Image::TYPE_CATEGORY, $flushIDs);
    }

    /**
     * @param stdClass $image
     * @param string   $extension
     * @return string
     */
    private function getPropertiesImageName(stdClass $image, string $extension): string
    {
        if (empty($image->kEigenschaftWert) || !$this->config['bilder_variation_namen']) {
            return (\stripos(\strrev($image->cPfad), \strrev($extension)) === 0)
                ? $image->cPfad
                : $image->cPfad . '.' . $extension;
        }
        $propValue = $this->db->getSingleObject(
            'SELECT kEigenschaftWert, cArtNr, cName, kEigenschaft
                FROM teigenschaftwert
                WHERE kEigenschaftWert = :aid',
            ['aid' => $image->kEigenschaftWert]
        );
        if ($propValue === null) {
            $this->logger->warning(
                'Eigenschaftswertbild fuer nicht existierenden Eigenschaftswert {id}',
                ['id' => $image->kEigenschaftWert]
            );
            return $image->cPfad;
        }
        $imageName = $propValue->kEigenschaftWert;
        if ($propValue->cName) {
            switch ($this->config['bilder_variation_namen']) {
                case 1:
                    if (!empty($propValue->cArtNr)) {
                        $imageName = 'var' . $this->convertUmlauts($propValue->cArtNr);
                    }
                    break;

                case 2:
                    $product = $this->db->getSingleObject(
                        "SELECT tartikel.cArtNr, tartikel.cBarcode, tartikel.cName, tseo.cSeo
                            FROM teigenschaftwert, teigenschaft, tartikel
                            JOIN tseo
                                ON tseo.cKey = 'kArtikel'
                                AND tseo.kKey = tartikel.kArtikel
                            JOIN tsprache
                                ON tsprache.kSprache = tseo.kSprache
                            WHERE teigenschaftwert.kEigenschaft = teigenschaft.kEigenschaft
                                AND tsprache.cShopStandard = 'Y'
                                AND teigenschaft.kArtikel = tartikel.kArtikel
                                AND teigenschaftwert.kEigenschaftWert = :cid",
                        ['cid' => $image->kEigenschaftWert]
                    );
                    if ($product !== null && !empty($product->cArtNr) && !empty($propValue->cArtNr)) {
                        $imageName = $this->convertUmlauts($product->cArtNr) .
                            '_' .
                            $this->convertUmlauts($propValue->cArtNr);
                    }
                    break;

                case 3:
                    $product = $this->db->getSingleObject(
                        "SELECT tartikel.cArtNr, tartikel.cBarcode, tartikel.cName, tseo.cSeo
                            FROM teigenschaftwert, teigenschaft, tartikel
                            JOIN tseo
                                ON tseo.cKey = 'kArtikel'
                                AND tseo.kKey = tartikel.kArtikel
                            JOIN tsprache
                                ON tsprache.kSprache = tseo.kSprache
                            WHERE teigenschaftwert.kEigenschaft = teigenschaft.kEigenschaft
                                AND tsprache.cShopStandard = 'Y'
                                AND teigenschaft.kArtikel = tartikel.kArtikel
                                AND teigenschaftwert.kEigenschaftWert = :cid",
                        ['cid' => $image->kEigenschaftWert]
                    );

                    $attribute = $this->db->getSingleObject(
                        'SELECT cName FROM teigenschaft WHERE kEigenschaft = :aid',
                        ['aid' => $propValue->kEigenschaft]
                    );
                    if ($attribute !== null
                        && (!empty($product->cSeo) || !empty($product->cName))
                        && !empty($attribute->cName)
                        && !empty($propValue->cName)
                    ) {
                        if ($product->cSeo) {
                            $imageName = $product->cSeo . '_' .
                                $this->convertUmlauts($attribute->cName) . '_' .
                                $this->convertUmlauts($propValue->cName);
                        } else {
                            $imageName = $this->convertUmlauts($product->cName) . '_' .
                                $this->convertUmlauts($attribute->cName) . '_' .
                                $this->convertUmlauts($propValue->cName);
                        }
                    }
                    break;
            }
        }

        return $this->removeSpecialChars($imageName) . '.' . $extension;
    }

    /**
     * @param stdClass $image
     * @param string   $ext
     * @return string
     */
    private function getCategoryImageName(stdClass $image, string $ext): string
    {
        $imageName = $image->cPfad;
        if (empty($image->kKategorie) || !$this->config['bilder_kategorie_namen']) {
            return $this->getNewFilename((\pathinfo($imageName, \PATHINFO_FILENAME)) . '.' . $ext);
        }
        $data = $this->db->getSingleObject(
            "SELECT tseo.cSeo, tkategorie.cName
                FROM tkategorie
                JOIN tseo
                    ON tseo.cKey = 'kKategorie'
                    AND tseo.kKey = tkategorie.kKategorie
                JOIN tsprache
                    ON tsprache.kSprache = tseo.kSprache
                WHERE tkategorie.kKategorie = :cid
                    AND tsprache.cShopStandard = 'Y'",
            ['cid' => (int)$image->kKategorie]
        );
        if ($data !== null && !empty($data->cName) && (int)$this->config['bilder_kategorie_namen'] === 1) {
            $imageName = $this->removeSpecialChars($data->cSeo ?: $this->convertUmlauts($data->cName)) . '.' . $ext;
        } else {
            $imageName = \pathinfo($image->cPfad, \PATHINFO_FILENAME) . '.' . $ext;
        }

        return $this->getNewFilename($imageName);
    }

    /**
     * @param stdClass $image
     * @param string   $ext
     * @return string
     */
    private function getManufacturerImageName(stdClass $image, string $ext): string
    {
        $data = $this->db->getSingleObject(
            'SELECT cName, cSeo
                FROM thersteller
                WHERE kHersteller = :mid',
            ['mid' => $image->kHersteller]
        );
        if ($data !== null && !empty($data->cSeo) && (int)$this->config['bilder_hersteller_namen'] === 1) {
            $imageName = $this->removeSpecialChars($data->cSeo ?: $this->convertUmlauts($data->cName)) . '.' . $ext;
        } else {
            $imageName = \pathinfo($image->cPfad, \PATHINFO_FILENAME) . '.' . $ext;
        }

        return $this->getNewFilename($imageName);
    }

    /**
     * @param stdClass $image
     * @param string   $ext
     * @return string
     */
    private function getCharacteristicValueImageName(stdClass $image, string $ext): string
    {
        $conf = (int)$this->config['bilder_merkmalwert_namen'];
        if ($conf === 2) {
            $imageName = $image->cPfad . '.' . $ext;
        } else {
            $data = $this->db->getSingleObject(
                'SELECT tmerkmalwertsprache.cSeo, tmerkmalwertsprache.cWert
                    FROM tmerkmalwertsprache
                    JOIN tsprache
                        ON tsprache.kSprache = tmerkmalwertsprache.kSprache
                    WHERE kMerkmalWert = :cid
                        AND tsprache.cShopStandard = \'Y\'',
                ['cid' => $image->kMerkmalWert]
            );
            if ($data !== null && !empty($data->cSeo) && $conf === 1) {
                $imageName = $this->removeSpecialChars($data->cSeo ?: $this->convertUmlauts($data->cName)) . '.' . $ext;
            } else {
                $imageName = \pathinfo($image->cPfad, \PATHINFO_FILENAME) . '.' . $ext;
            }
        }

        return $this->getNewFilename($imageName);
    }

    /**
     * @param stdClass $image
     * @param string   $ext
     * @return string
     */
    private function getCharacteristicImageName(stdClass $image, string $ext): string
    {
        $conf = (int)$this->config['bilder_merkmal_namen'];
        if ($conf === 2) {
            $imageName = $image->cPfad . '.' . $ext;
        } else {
            $data = $this->db->getSingleObject(
                'SELECT cName
                    FROM tmerkmal
                    WHERE kMerkmal = :cid',
                ['cid' => $image->kMerkmal]
            );
            if ($data !== null && !empty($data->cName) && $conf === 1) {
                $imageName = $this->removeSpecialChars($this->convertUmlauts($data->cName)) . '.' . $ext;
            } else {
                $imageName = \pathinfo($image->cPfad, \PATHINFO_FILENAME) . '.' . $ext;
            }
        }

        return $this->getNewFilename($imageName);
    }

    /**
     * @param string $str
     * @return string
     */
    private function convertUmlauts(string $str): string
    {
        $src = ['ä', 'ö', 'ü', 'ß', 'Ä', 'Ö', 'Ü'];
        $rpl = ['ae', 'oe', 'ue', 'ss', 'AE', 'OE', 'UE'];

        return \str_replace($src, $rpl, $str);
    }

    /**
     * @param string $str
     * @return string
     */
    private function removeSpecialChars(string $str): string
    {
        $str = \str_replace(['/', ' '], '-', $str);

        return \preg_replace('/[^a-zA-Z\d\.\-_]/', '', $str);
    }

    /**
     * @param array $xml
     */
    private function handleDeletes(array $xml): void
    {
        \executeHook(\HOOK_BILDER_XML_BEARBEITEDELETES, [
            'Kategorie'        => $xml['del_bilder']['kKategoriePict'] ?? [],
            'KategoriePK'      => $xml['del_bilder']['kKategorie'] ?? [],
            'Eigenschaftswert' => $xml['del_bilder']['kEigenschaftWertPict'] ?? [],
            'Hersteller'       => $xml['del_bilder']['kHersteller'] ?? [],
            'Merkmal'          => $xml['del_bilder']['kMerkmal'] ?? [],
            'Merkmalwert'      => $xml['del_bilder']['kMerkmalWert'] ?? [],
        ]);
        // Kategoriebilder löschen Wawi > .99923
        $this->deleteCategoryImages($xml);
        // Variationsbilder löschen Wawi > .99923
        $this->deleteVariationImages($xml);
        // Herstellerbilder löschen
        $this->deleteManufacturerImages($xml);
        // Merkmalbilder löschen
        $this->deleteCharacteristicImages($xml);
        // Merkmalwertbilder löschen
        $this->deleteCharacteristicValueImages($xml);
    }

    /**
     * @param array $xml
     */
    private function deleteVariationImages(array $xml): void
    {
        $source = $xml['del_bilder']['kEigenschaftWert'] ?? [];
        if (\is_numeric($source)) {
            $source = [$source];
        }
        foreach (\array_filter(\array_map('\intval', $source)) as $id) {
            $this->db->delete('teigenschaftwertpict', 'kEigenschaftWert', $id);
        }
    }

    /**
     * @param array $xml
     */
    private function deleteCategoryImages(array $xml): void
    {
        $source = $xml['del_bilder']['kKategorie'] ?? [];
        if (\is_numeric($source)) {
            $source = [$source];
        }
        $ids = \array_filter(\array_map('\intval', $source));
        foreach ($ids as $id) {
            $this->db->delete('tkategoriepict', 'kKategorie', $id);
        }
        $this->clearImageCache(Image::TYPE_CATEGORY, $ids);
    }

    /**
     * @param array $xml
     */
    private function deleteManufacturerImages(array $xml): void
    {
        $cacheTags = [];
        $source    = $xml['del_bilder']['kHersteller'] ?? [];
        if (\is_numeric($source)) {
            $source = [$source];
        }
        $ids = \array_filter(\array_map('\intval', $source));
        foreach ($ids as $manufacturerID) {
            $this->db->update(
                'thersteller',
                'kHersteller',
                (int)$manufacturerID,
                (object)['cBildpfad' => '']
            );
            foreach ($this->db->selectAll(
                'tartikel',
                'kHersteller',
                (int)$manufacturerID,
                'kArtikel'
            ) as $product) {
                $cacheTags[] = \CACHING_GROUP_ARTICLE . '_' . (int)$product->kArtikel;
            }
        }
        $this->cache->flushTags($cacheTags);
        $this->clearImageCache(Image::TYPE_MANUFACTURER, $ids);
    }

    /**
     * @param array $xml
     */
    private function deleteCharacteristicImages(array $xml): void
    {
        $source = $xml['del_bilder']['kMerkmal'] ?? [];
        if (\is_numeric($source)) {
            $source = [$source];
        }
        $ids = \array_filter(\array_map('\intval', $source));
        foreach ($ids as $attrID) {
            $this->db->update(
                'tmerkmal',
                'kMerkmal',
                (int)$attrID,
                (object)['cBildpfad' => '']
            );
        }
        $this->clearImageCache(Image::TYPE_CHARACTERISTIC, $ids);
    }

    /**
     * @param array $xml
     */
    private function deleteCharacteristicValueImages(array $xml): void
    {
        $source = $xml['del_bilder']['kMerkmalWert'] ?? [];
        if (\is_numeric($source)) {
            $source = [$source];
        }
        $ids = \array_filter(\array_map('\intval', $source));
        foreach ($ids as $attrValID) {
            $this->db->update(
                'tmerkmalwert',
                'kMerkmalWert',
                (int)$attrValID,
                (object)['cBildpfad' => '']
            );
            $this->db->delete('tmerkmalwertbild', 'kMerkmalWert', (int)$attrValID);
        }
        $this->clearImageCache(Image::TYPE_CHARACTERISTIC_VALUE, $ids);
    }

    /**
     * @param resource|\GdImage $im
     * @param stdClass|null     $config
     * @return mixed
     */
    private function brandImage($im, ?stdClass $config)
    {
        if ($config === null
            || (isset($config->nAktiv) && (int)$config->nAktiv === 0)
            || !isset($config->cBrandingBild)
        ) {
            return $im;
        }
        $brandingImage = \PFAD_ROOT . \PFAD_BRANDINGBILDER . $config->cBrandingBild;
        if (!\file_exists($brandingImage)) {
            return $im;
        }
        $position     = $config->cPosition;
        $transparency = (int)$config->dTransparenz;
        $brandingSize = (int)$config->dGroesse;
        $margin       = (int)($config->dRandabstand / 100);
        $branding     = $this->createImage($brandingImage, 0, 0, true);
        if (!$im || !$branding) {
            return $im;
        }
        $imageWidth        = \imagesx($im);
        $imageHeight       = \imagesy($im);
        $brandingWidth     = \imagesx($branding);
        $brandingHeight    = \imagesy($branding);
        $brandingNewWidth  = $brandingWidth;
        $brandingNewHeight = $brandingHeight;
        $srcImage          = $branding;
        if ($brandingSize > 0) { // scale to width
            $brandingNewWidth  = (int)\round(($imageWidth * $brandingSize) / 100.0);
            $brandingNewHeight = (int)\round(($brandingNewWidth / $brandingWidth) * $brandingHeight);
            $srcImage          = $this->createImage($brandingImage, $brandingNewWidth, $brandingNewHeight, true);
        }

        [$brandingPosX, $brandingPosY] = $this->getBrandingCoordinates(
            $position,
            $imageWidth,
            $imageHeight,
            $brandingNewWidth,
            $brandingNewHeight,
            $margin
        );
        \imagealphablending($im, true);
        \imagesavealpha($im, true);
        $this->imagecopymergeAlpha(
            $im,
            $srcImage,
            $brandingPosX,
            $brandingPosY,
            0,
            0,
            $brandingNewWidth,
            $brandingNewHeight,
            100 - $transparency
        );

        return $im;
    }

    /**
     * @param string $position
     * @param int    $imageWidth
     * @param int    $imageHeight
     * @param int    $brandingNewWidth
     * @param int    $brandingNewHeight
     * @param int    $margin
     * @return array
     */
    private function getBrandingCoordinates(
        string $position,
        int $imageWidth,
        int $imageHeight,
        int $brandingNewWidth,
        int $brandingNewHeight,
        int $margin
    ): array {
        switch ($position) {
            case 'top':
                $positionX = $imageWidth / 2 - $brandingNewWidth / 2;
                $positionY = $imageHeight * $margin;
                break;
            case 'top-right':
                $positionX = $imageWidth - $brandingNewWidth - $imageWidth * $margin;
                $positionY = $imageHeight * $margin;
                break;
            case 'right':
                $positionX = $imageWidth - $brandingNewWidth - $imageWidth * $margin;
                $positionY = $imageHeight / 2 - $brandingNewHeight / 2;
                break;
            case 'bottom-right':
                $positionX = $imageWidth - $brandingNewWidth - $imageWidth * $margin;
                $positionY = $imageHeight - $brandingNewHeight - $imageHeight * $margin;
                break;
            case 'bottom':
                $positionX = $imageWidth / 2 - $brandingNewWidth / 2;
                $positionY = $imageHeight - $brandingNewHeight - $imageHeight * $margin;
                break;
            case 'bottom-left':
                $positionX = $imageWidth * $margin;
                $positionY = $imageHeight - $brandingNewHeight - $imageHeight * $margin;
                break;
            case 'left':
                $positionX = $imageWidth * $margin;
                $positionY = $imageHeight / 2 - $brandingNewHeight / 2;
                break;
            case 'top-left':
                $positionX = $imageWidth * $margin;
                $positionY = $imageHeight * $margin;
                break;
            case 'center':
                $positionX = $imageWidth / 2 - $brandingNewWidth / 2;
                $positionY = $imageHeight / 2 - $brandingNewHeight / 2;
                break;
            default:
                $positionX = 0;
                $positionY = 0;
                break;
        }

        return [(int)\round($positionX), (int)\round($positionY)];
    }

    /**
     * @param resource|\GdImage $destImg
     * @param resource|\GdImage $srcImg
     * @param int               $destX
     * @param int               $destY
     * @param int               $srcX
     * @param int               $srxY
     * @param int               $srcW
     * @param int               $srcH
     * @param int               $pct
     * @return bool
     */
    private function imagecopymergeAlpha(
        $destImg,
        $srcImg,
        int $destX,
        int $destY,
        int $srcX,
        int $srxY,
        int $srcW,
        int $srcH,
        int $pct
    ): bool {
        $pct /= 100;
        // Get image width and height
        $w = \imagesx($srcImg);
        $h = \imagesy($srcImg);
        // Turn alpha blending off
        \imagealphablending($srcImg, false);
        // loop through image pixels and modify alpha
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                // get current alpha value (represents the transparency!)
                $colorxy = \imagecolorat($srcImg, $x, $y);
                $alpha   = ($colorxy >> 24) & 0xFF;
                // calculate new alpha
                $alpha = 127 + 127 * $pct * ($alpha - 127) / 127;
                // get the color index with new alpha
                $alphacolorxy = \imagecolorallocatealpha(
                    $srcImg,
                    ($colorxy >> 16) & 0xFF,
                    ($colorxy >> 8) & 0xFF,
                    $colorxy & 0xFF,
                    (int)$alpha
                );
                // set pixel with the new color + opacity
                if (!\imagesetpixel($srcImg, $x, $y, $alphacolorxy)) {
                    return false;
                }
            }
        }
        \imagecopy($destImg, $srcImg, $destX, $destY, $srcX, $srxY, $srcW, $srcH);

        return true;
    }

    /**
     * @param string $filename
     * @return string|null
     */
    private function getExtension(string $filename): ?string
    {
        if (!\file_exists($filename)) {
            return null;
        }
        $size = \getimagesize($filename);

        return match ($size[2]) {
            \IMAGETYPE_JPEG => 'jpg',
            \IMAGETYPE_PNG  => \function_exists('imagecreatefrompng') ? 'png' : false,
            \IMAGETYPE_GIF  => \function_exists('imagecreatefromgif') ? 'gif' : false,
            \IMAGETYPE_BMP  => \function_exists('imagecreatefromwbmp') ? 'bmp' : false,
            default         => null,
        };
    }

    /**
     * @param string|null $sourcePath
     * @return string
     */
    private function getNewExtension(string $sourcePath = null): string
    {
        $config = \mb_convert_case($this->config['bilder_dateiformat'], \MB_CASE_LOWER);

        return $config === 'auto'
            ? \pathinfo($sourcePath)['extension'] ?? 'jpg'
            : $config;
    }

    /**
     * @param string   $source
     * @param int      $width
     * @param int      $height
     * @param bool     $branding
     * @param int|null $containerWidth
     * @param int|null $containerHeight
     * @return false|resource|\GdImage
     */
    private function createImage(
        string $source,
        int $width = 0,
        int $height = 0,
        bool $branding = false,
        int $containerWidth = null,
        int $containerHeight = null
    ) {
        $imgInfo = \getimagesize($source);
        $im      = match ($imgInfo[2]) {
            \IMAGETYPE_GIF => \imagecreatefromgif($source),
            \IMAGETYPE_PNG => \imagecreatefrompng($source),
            default        => \imagecreatefromjpeg($source),
        };
        if ($width === 0 && $height === 0) {
            [$width, $height] = $imgInfo;
        }
        $posX   = 0;
        $posY   = 0;
        $width  = (int)\round($width);
        $height = (int)\round($height);
        $newImg = \imagecreatetruecolor($containerWidth ?? $width, $containerHeight ?? $height);
        if (!$newImg) {
            return $im;
        }
        if ($this->getNewExtension($source) === 'jpg') {
            $rgb   = $this->html2rgb($this->config['bilder_hintergrundfarbe']);
            $color = \imagecolorallocate($newImg, $rgb[0], $rgb[1], $rgb[2]);
            \imagealphablending($newImg, $branding);
        } else {
            $color = \imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            \imagealphablending($newImg, false);
        }

        \imagesavealpha($newImg, true);
        \imagefilledrectangle($newImg, 0, 0, $containerWidth ?? $width, $containerHeight ?? $height, $color);
        if ($containerHeight !== null) {
            $posX = ($containerWidth / 2) - ($width / 2);
            $posY = ($containerHeight / 2) - ($height / 2);
        }
        \imagecopyresampled($newImg, $im, (int)$posX, (int)$posY, 0, 0, $width, $height, $imgInfo[0], $imgInfo[1]);

        return $newImg;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getNewFilename(string $path): string
    {
        return \pathinfo($path, \PATHINFO_FILENAME) . '.' . $this->getNewExtension($path);
    }

    /**
     * @param resource|\GdImage $im
     * @param string            $format
     * @param string            $path
     * @param int               $quality
     * @return bool
     */
    private function saveImage($im, string $format, string $path, int $quality = 80): bool
    {
        if (!$im) {
            return false;
        }
        $res = match (\strtolower($format)) {
            'jpg'   => \function_exists('imagejpeg') && \imagejpeg($im, $path, $quality),
            'png'   => \function_exists('imagepng') && \imagepng($im, $path),
            'gif'   => \function_exists('imagegif') && \imagegif($im, $path),
            'bmp'   => \function_exists('imagewbmp') && \imagewbmp($im, $path),
            default => false,
        };
        if ($res !== false) {
            @\chmod($path, 0644);
        } else {
            $this->logger->error('Cannot save image: ' . $path);
        }

        return $res;
    }

    /**
     * @param string $color
     * @return array
     */
    private function html2rgb(string $color): array
    {
        if (\str_starts_with($color, '#')) {
            $color = \substr($color, 1);
        }
        if (\strlen($color) === 6) {
            [$r, $g, $b] = [
                $color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5]
            ];
        } elseif (\strlen($color) === 3) {
            [$r, $g, $b] = [
                $color[0] . $color[0],
                $color[1] . $color[1],
                $color[2] . $color[2]
            ];
        } elseif (\str_starts_with($color, 'rgb')) {
            if (\str_starts_with($color, 'rgba(')) {
                $color = Image::rgba2rgb($color);
            }
            $rgbaColor = \explode(',', \rtrim(\substr($color, \strlen('rgb(')), ')'));

            return [
                (int)\trim($rgbaColor[0]),
                (int)\trim($rgbaColor[1]),
                (int)\trim($rgbaColor[2])
            ];
        } else {
            [$r, $g, $b] = ['ff', 'ff', 'ff'];
        }

        return [\hexdec($r), \hexdec($g), \hexdec($b)];
    }

    /**
     * @param string $class
     * @param array  $ids
     * @return bool
     */
    private function clearImageCache(string $class, array $ids): bool
    {
        if (\count($ids) === 0) {
            return false;
        }
        $instance = Media::getClass($class);
        /** @var IMedia $instance */
        return $instance::clearCache($ids);
    }
}
