<?php declare(strict_types=1);

namespace Template\NOVA;

use Illuminate\Support\Collection;
use JTL\Cache\JTLCacheInterface;
use JTL\Catalog\Category\Kategorie;
use JTL\Catalog\Category\KategorieListe;
use JTL\Catalog\Product\Artikel;
use JTL\Catalog\Product\Preise;
use JTL\Catalog\Product\VariationValue;
use JTL\CheckBox;
use JTL\DB\DbInterface;
use JTL\Filter\Config;
use JTL\Filter\ProductFilter;
use JTL\Helpers\Category;
use JTL\Helpers\Manufacturer;
use JTL\Helpers\Seo;
use JTL\Helpers\Tax;
use JTL\Link\Link;
use JTL\Link\LinkGroupInterface;
use JTL\Media\Image;
use JTL\Media\Image\Product;
use JTL\Media\MultiSizeImage;
use JTL\Session\Frontend;
use JTL\Shop;
use JTL\Staat;
use Smarty_Internal_Template;
use stdClass;
use function Functional\first;

/**
 * Class Plugins
 * @package Template\NOVA
 */
class Plugins
{
    /**
     * @var DbInterface
     */
    private DbInterface $db;

    /**
     * @var JTLCacheInterface
     */
    private JTLCacheInterface $cache;

    /**
     * Plugins constructor.
     * @param DbInterface       $db
     * @param JTLCacheInterface $cache
     */
    public function __construct(DbInterface $db, JTLCacheInterface $cache)
    {
        $this->db    = $db;
        $this->cache = $cache;
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return array|void
     */
    public function getProductList(array $params, Smarty_Internal_Template $smarty)
    {
        $limit                 = (int)($params['nLimit'] ?? 10);
        $sort                  = (int)($params['nSortierung'] ?? 0);
        $assignTo              = (isset($params['cAssign']) && \strlen($params['cAssign']) > 0)
            ? $params['cAssign']
            : 'oCustomArtikel_arr';
        $characteristicFilters = isset($params['cMerkmalFilter'])
            ? ProductFilter::initCharacteristicFilter(\explode(';', $params['cMerkmalFilter']))
            : [];
        $searchFilters         = isset($params['cSuchFilter'])
            ? ProductFilter::initSearchFilter(\explode(';', $params['cSuchFilter']))
            : [];
        $params                = [
            'kKategorie'             => $params['kKategorie'] ?? null,
            'kHersteller'            => $params['kHersteller'] ?? null,
            'kArtikel'               => $params['kArtikel'] ?? null,
            'kVariKindArtikel'       => $params['kVariKindArtikel'] ?? null,
            'kSeite'                 => $params['kSeite'] ?? null,
            'kSuchanfrage'           => $params['kSuchanfrage'] ?? null,
            'kMerkmalWert'           => $params['kMerkmalWert'] ?? null,
            'kSuchspecial'           => $params['kSuchspecial'] ?? null,
            'kKategorieFilter'       => $params['kKategorieFilter'] ?? null,
            'kHerstellerFilter'      => $params['kHerstellerFilter'] ?? null,
            'nBewertungSterneFilter' => $params['nBewertungSterneFilter'] ?? null,
            'cPreisspannenFilter'    => $params['cPreisspannenFilter'] ?? '',
            'kSuchspecialFilter'     => $params['kSuchspecialFilter'] ?? null,
            'nSortierung'            => $sort,
            'MerkmalFilter_arr'      => $characteristicFilters,
            'SuchFilter_arr'         => $searchFilters,
            'nArtikelProSeite'       => $params['nArtikelProSeite'] ?? null,
            'cSuche'                 => $params['cSuche'] ?? null,
            'seite'                  => $params['seite'] ?? null
        ];
        if ($params['kArtikel'] !== null) {
            $products = [];
            if (!\is_array($params['kArtikel'])) {
                $params['kArtikel'] = [$params['kArtikel']];
            }
            $customerGroup = Frontend::getCustomerGroup();
            $currency      = Frontend::getCurrency();
            $options       = Artikel::getDefaultOptions();
            foreach ($params['kArtikel'] as $productID) {
                $product    = new Artikel($this->db, $customerGroup, $currency, $this->cache);
                $products[] = $product->fuelleArtikel($productID, $options);
            }
        } else {
            $products = (new ProductFilter(
                Config::getDefault(),
                $this->db,
                $this->cache
            ))
                ->initStates($params)
                ->generateSearchResults(null, true, $limit)
                ->getProducts()
                ->all();
        }

        $smarty->assign($assignTo, $products);

        if (isset($params['bReturn'])) {
            return $products;
        }
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return string|void
     */
    public function getStaticRoute(array $params, Smarty_Internal_Template $smarty)
    {
        if (!isset($params['id'])) {
            return;
        }
        $full   = ($params['full'] ?? true) === true;
        $secure = ($params['secure'] ?? false) === true;
        $url    = Shop::Container()->getLinkService()->getStaticRoute($params['id'], $full, $secure);
        $qp     = (array)($params['params'] ?? []);
        if (\count($qp) > 0) {
            $url .= (\parse_url($url, \PHP_URL_QUERY) ? '&' : '?') . \http_build_query($qp, '', '&');
        }
        if (!isset($params['assign'])) {
            return $url;
        }
        $smarty->assign($params['assign'], $url);
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return array|void
     */
    public function getManufacturers(array $params, Smarty_Internal_Template $smarty)
    {
        $manufacturers = Manufacturer::getInstance()->getManufacturers();
        if (!isset($params['assign'])) {
            return $manufacturers;
        }
        $smarty->assign($params['assign'], $manufacturers);
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return array|void
     */
    public function getBoxesByPosition(array $params, Smarty_Internal_Template $smarty)
    {
        if (!isset($params['position'])) {
            return;
        }
        $data  = Shop::Container()->getBoxService()->getBoxes();
        $boxes = $data[$params['position']] ?? [];
        if (!isset($params['assign'])) {
            return $boxes;
        }
        $smarty->assign($params['assign'], $boxes);
    }

    /**
     * @param array                    $params - categoryId mainCategoryId. 0 for first level categories
     * @param Smarty_Internal_Template $smarty
     * @return array|void
     */
    public function getCategoryArray(array $params, Smarty_Internal_Template $smarty)
    {
        $id = isset($params['categoryId']) ? (int)$params['categoryId'] : 0;
        if ($id === 0) {
            $categories = Category::getInstance();
            $list       = $categories->combinedGetAll();
        } else {
            $categories = new KategorieListe();
            $list       = $categories->getAllCategoriesOnLevel($id);
        }
        if (isset($params['categoryBoxNumber']) && (int)$params['categoryBoxNumber'] > 0) {
            $list2 = [];
            foreach ($list as $key => $item) {
                if ($item->getCategoryFunctionAttribute(\KAT_ATTRIBUT_KATEGORIEBOX) == $params['categoryBoxNumber']) {
                    $list2[$key] = $item;
                }
            }
            $list = $list2;
        }

        if (isset($params['assign'])) {
            $smarty->assign($params['assign'], $list);

            return;
        }

        return $list;
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return array|void
     */
    public function getCategoryParents(array $params, Smarty_Internal_Template $smarty)
    {
        $id         = (int)($params['categoryId'] ?? 0);
        $categories = new KategorieListe();
        $list       = $categories->getOpenCategories(new Kategorie($id));
        \array_shift($list);
        $list = \array_reverse($list);
        if (!isset($params['assign'])) {
            return $list;
        }
        $smarty->assign($params['assign'], $list);
    }

    /**
     * @param array $params
     * @return string
     */
    public function getImgTag(array $params): string
    {
        if (empty($params['src'])) {
            return '';
        }
        $size = $this->getImageSize($params['src']);

        $url   = $params['src'];
        $id    = isset($params['id']) ? ' id="' . $params['id'] . '"' : '';
        $alt   = isset($params['alt']) ? ' alt="' . $this->truncate($params['alt'], 75) . '"' : '';
        $title = isset($params['title']) ? ' title="' . $this->truncate($params['title'], 75) . '"' : '';
        $class = isset($params['class']) ? ' class="' . $this->truncate($params['class'], 75) . '"' : '';
        if (!\str_starts_with($url, 'http')) {
            $url = Shop::getImageBaseURL() . \ltrim($url, '/');
        }
        if ($size !== null && $size->size->width > 0 && $size->size->height > 0) {
            return '<img src="' . $url . '" width="' . $size->size->width . '" height="'
                . $size->size->height . '"' . $id . $alt . $title . $class . ' />';
        }

        return '<img src="' . $url . '"' . $id . $alt . $title . $class . ' />';
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     */
    public function hasBoxes(array $params, Smarty_Internal_Template $smarty): void
    {
        $boxData = $smarty->getTemplateVars('boxes');
        $smarty->assign($params['assign'], !empty($boxData[$params['position']]));
    }

    /**
     * @param string $text
     * @param int    $length
     * @return string
     */
    public function truncate(string $text, int $length): string
    {
        if (\strlen($text) > $length) {
            $text  = \substr($text, 0, $length);
            $text  = \substr($text, 0, \strrpos($text, ' '));
            $text .= '...';
        }

        return $text;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function getLocalizedPrice(array $params)
    {
        $surcharge                     = new stdClass();
        $surcharge->cAufpreisLocalized = '';
        $surcharge->cPreisInklAufpreis = '';
        if ((float)$params['fAufpreisNetto'] != 0) {
            $currency       = Frontend::getCurrency();
            $netSurcharge   = (float)$params['fAufpreisNetto'];
            $fVKNetto       = (float)$params['fVKNetto'];
            $kSteuerklasse  = (int)$params['kSteuerklasse'];
            $fVPEWert       = (float)$params['fVPEWert'];
            $cVPEEinheit    = $params['cVPEEinheit'];
            $funcAttributes = $params['FunktionsAttribute'];
            $precision      = (isset($funcAttributes[\FKT_ATTRIBUT_GRUNDPREISGENAUIGKEIT])
                && (int)$funcAttributes[\FKT_ATTRIBUT_GRUNDPREISGENAUIGKEIT] > 0)
                ? (int)$funcAttributes[\FKT_ATTRIBUT_GRUNDPREISGENAUIGKEIT]
                : 2;

            if ((int)$params['nNettoPreise'] === 1) {
                $surcharge->cAufpreisLocalized = Preise::getLocalizedPriceString($netSurcharge, $currency);
                $surcharge->cPreisInklAufpreis = Preise::getLocalizedPriceString($netSurcharge + $fVKNetto, $currency);
                $surcharge->cAufpreisLocalized = ($netSurcharge > 0)
                    ? ('+ ' . $surcharge->cAufpreisLocalized)
                    : \str_replace('-', '- ', $surcharge->cAufpreisLocalized);

                if ($fVPEWert > 0) {
                    $surcharge->cPreisVPEWertAufpreis     = Preise::getLocalizedPriceString(
                            $netSurcharge / $fVPEWert,
                            $currency,
                            true,
                            $precision
                        ) . ' ' . Shop::Lang()->get('vpePer') . ' ' . $cVPEEinheit;
                    $surcharge->cPreisVPEWertInklAufpreis = Preise::getLocalizedPriceString(
                            ($netSurcharge + $fVKNetto) / $fVPEWert,
                            $currency,
                            true,
                            $precision
                        ) . ' ' . Shop::Lang()->get('vpePer') . ' ' . $cVPEEinheit;

                    $surcharge->cAufpreisLocalized .= ', ' . $surcharge->cPreisVPEWertAufpreis;
                    $surcharge->cPreisInklAufpreis .= ', ' . $surcharge->cPreisVPEWertInklAufpreis;
                }
            } else {
                $surcharge->cAufpreisLocalized = Preise::getLocalizedPriceString(
                    Tax::getGross($netSurcharge, $_SESSION['Steuersatz'][$kSteuerklasse], 4),
                    $currency
                );
                $surcharge->cPreisInklAufpreis = Preise::getLocalizedPriceString(
                    Tax::getGross($netSurcharge + $fVKNetto, $_SESSION['Steuersatz'][$kSteuerklasse], 4),
                    $currency
                );
                $surcharge->cAufpreisLocalized = ($netSurcharge > 0)
                    ? ('+ ' . $surcharge->cAufpreisLocalized)
                    : \str_replace('-', '- ', $surcharge->cAufpreisLocalized);

                if ($fVPEWert > 0) {
                    $surcharge->cPreisVPEWertAufpreis     = Preise::getLocalizedPriceString(
                            Tax::getGross($netSurcharge / $fVPEWert, $_SESSION['Steuersatz'][$kSteuerklasse]),
                            $currency,
                            true,
                            $precision
                        ) . ' ' . Shop::Lang()->get('vpePer') . ' ' . $cVPEEinheit;
                    $surcharge->cPreisVPEWertInklAufpreis = Preise::getLocalizedPriceString(
                            Tax::getGross(
                                ($netSurcharge + $fVKNetto) / $fVPEWert,
                                $_SESSION['Steuersatz'][$kSteuerklasse]
                            ),
                            $currency,
                            true,
                            $precision
                        ) . ' ' . Shop::Lang()->get('vpePer') . ' ' . $cVPEEinheit;

                    $surcharge->cAufpreisLocalized .= ', ' . $surcharge->cPreisVPEWertAufpreis;
                    $surcharge->cPreisInklAufpreis .= ', ' . $surcharge->cPreisVPEWertInklAufpreis;
                }
            }
        }

        return (isset($params['bAufpreise']) && (int)$params['bAufpreise'] > 0)
            ? $surcharge->cAufpreisLocalized
            : $surcharge->cPreisInklAufpreis;
    }

    /**
     * @param int $location
     * @param int $languageID
     * @return CheckBox[]
     */
    private function getCheckboxes(int $location, int $languageID): array
    {
        $cid        = 'cb_' . $location . '_' . $languageID;
        $checkBoxes = Shop::has($cid)
            ? Shop::get($cid)
            : (new CheckBox(0, $this->db))->getCheckBoxFrontend($location, 0, true, true);
        Shop::set($cid, $checkBoxes);

        return $checkBoxes;
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     */
    public function hasCheckBoxForLocation(array $params, Smarty_Internal_Template $smarty): void
    {
        $smarty->assign(
            $params['bReturn'],
            \count($this->getCheckboxes((int)$params['nAnzeigeOrt'], Shop::getLanguageID())) > 0
        );
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     */
    public function getCheckBoxForLocation(array $params, Smarty_Internal_Template $smarty): void
    {
        $langID     = Shop::getLanguageID();
        $checkboxes = $this->getCheckboxes((int)$params['nAnzeigeOrt'], $langID);
        foreach ($checkboxes as $key => $checkbox) {
            //SHOP-8036
            if (
                $checkbox->cName === CheckBox::CHECKBOX_DOWNLOAD_ORDER_COMPLETE
                && isset($smarty->tpl_vars['hasDownloads'])
                && $smarty->tpl_vars['hasDownloads']->value === false
            ) {
                unset($checkboxes[$key]);
                continue;
            }
            $url                     = $checkbox->kLink > 0
                ? $checkbox->getLink()->getURL()
                : '';
            $error                   = isset($params['cPlausi_arr'][$checkbox->cID]);
            $checkbox->isActive      = isset($params['cPost_arr'][$checkbox->cID]);
            $checkbox->identifier    = $checkbox->cName;
            $checkbox->cName         = $checkbox->oCheckBoxSprache_arr[$langID]->cText ?? '';
            $checkbox->cLinkURL      = $url;
            $checkbox->cLinkURLFull  = $url;
            $checkbox->cBeschreibung = !empty($checkbox->oCheckBoxSprache_arr[$langID]->cBeschreibung)
                ? $checkbox->oCheckBoxSprache_arr[$langID]->cBeschreibung
                : '';
            $checkbox->cErrormsg     = $error
                ? Shop::Lang()->get('pleasyAccept', 'account data')
                : '';
        }
        if (isset($params['assign'])) {
            $smarty->assign($params['assign'], $checkboxes);
        }
    }

    /**
     * @param array $params
     * @return string
     */
    public function aaURLEncode(array $params): string
    {
        $reset  = (int)($params['nReset'] ?? 0) === 1;
        $url    = $_SERVER['REQUEST_URI'];
        $params = ['&aaParams', '?aaParams', '&aaReset', '?aaReset'];
        $exists = false;
        foreach ($params as $param) {
            $exists = \strpos($url, $param);
            if ($exists !== false) {
                $url = \substr($url, 0, $exists);
                break;
            }
            $exists = false;
        }
        if ($exists !== false) {
            $url = \substr($url, 0, $exists);
        }
        if (isset($params['bUrlOnly']) && (int)$params['bUrlOnly'] === 1) {
            return $url;
        }
        $paramString = '';
        unset($params['nReset']);
        if (\is_array($params) && \count($params) > 0) {
            foreach ($params as $key => $param) {
                $paramString .= $key . '=' . $param . ';';
            }
        }

        $sep = (!\str_contains($url, '?')) ? '?' : '&';

        return $url . $sep . ($reset ? 'aaReset=' : 'aaParams=') . \base64_encode($paramString);
    }

    /**
     * @param array                    $params - ['type'] Templatename of link, ['assign'] array name to assign
     * @param Smarty_Internal_Template $smarty
     */
    public function getNavigation(array $params, Smarty_Internal_Template $smarty): void
    {
        if (!isset($params['assign'])) {
            return;
        }
        $identifier = $params['linkgroupIdentifier'];
        $linkGroup  = null;
        if (\strlen($identifier) > 0) {
            $linkGroups = Shop::Container()->getLinkService()->getVisibleLinkGroups();
            $linkGroup  = $linkGroups->getLinkgroupByTemplate($identifier);
        }
        if ($linkGroup !== null && $linkGroup->isAvailableInLanguage(Shop::getLanguageID())) {
            $smarty->assign($params['assign'], $this->buildNavigationSubs($linkGroup));
        }
    }

    /**
     * @param LinkGroupInterface $linkGroup
     * @param int                $parentID
     * @return Collection
     */
    public function buildNavigationSubs(LinkGroupInterface $linkGroup, int $parentID = 0): Collection
    {
        $links = new Collection();
        if ($linkGroup->getTemplate() === 'hidden' || $linkGroup->getName() === 'hidden') {
            return $links;
        }
        $activeLinkID = Shop::getState()->linkID;
        foreach ($linkGroup->getLinks() as $link) {
            /** @var Link $link */
            if ($link->getParent() !== $parentID) {
                continue;
            }
            $id  = $link->getID();
            $ref = $link->getReference();
            if ($ref > 0) {
                $id = $ref;
            }
            $link->setChildLinks($this->buildNavigationSubs($linkGroup, $id));
            $active = $link->getIsActive()
                || ($activeLinkID > 0 && ($activeLinkID === $link->getID() || $activeLinkID === $ref));
            $link->setIsActive($active);
            $links->push($link);
        }

        return $links;
    }

    /**
     * @param array $params
     * @return string|stdClass|null|false
     */
    public function prepareImageDetails(array $params)
    {
        if (!isset($params['item'])) {
            return null;
        }
        /** @var MultiSizeImage $item */
        $item = $params['item'];
        if (\method_exists($item, 'getDimensions')) {
            $result     = [];
            $images     = first($item->getImages()) ?? [];
            $dimensions = first($item->getDimensions()) ?? [];
            foreach (\array_keys($dimensions) as $size) {
                $result[$size] = (object)[
                    'src'  => $images[$size],
                    'size' => (object)[
                        'width'  => $dimensions[$size]['width'],
                        'height' => $dimensions[$size]['height']
                    ],
                    'type' => 0
                ];
            }
        } else {
            $result       = [
                'xs' => $this->getImageSize($item->cPfadMini),
                'sm' => $this->getImageSize($item->cPfadKlein),
                'md' => $this->getImageSize($item->cPfadNormal),
                'lg' => $this->getImageSize($item->cPfadGross)
            ];
            $imageBaseURL = Shop::getImageBaseURL();
            foreach ($result as $data) {
                if (isset($data->src) && !\str_starts_with($data->src, 'http')) {
                    $data->src = $imageBaseURL . $data->src;
                }
            }
        }

        if (isset($params['type'])) {
            $type = $params['type'];
            if (isset($result[$type])) {
                $result = $result[$type];
            }
        }
        $result = (object)$result;

        return (isset($params['json']) && $params['json'])
            ? \json_encode($result, \JSON_FORCE_OBJECT)
            : $result;
    }

    /**
     * @param string|null $image
     * @return stdClass|null
     */
    public function getImageSize(?string $image): ?stdClass
    {
        if ($image === null) {
            return null;
        }
        $path = \str_starts_with($image, \PFAD_BILDER)
            ? \PFAD_ROOT . $image
            : $image;
        if (\file_exists($path)) {
            [$width, $height, $type] = \getimagesize($path);
        } else {
            $req = Product::toRequest($path);

            if (!\is_object($req)) {
                return null;
            }

            $settings = Image::getSettings();
            $refImage = $req->getRaw();
            if ($refImage === null) {
                return null;
            }

            [$width, $height, $type] = \getimagesize($refImage);

            $size       = $settings['size'][$req->getSizeType()];
            $max_width  = $size['width'];
            $max_height = $size['height'];
            $old_width  = $width;
            $old_height = $height;

            $scale  = \min($max_width / $old_width, $max_height / $old_height);
            $width  = \ceil($scale * $old_width);
            $height = \ceil($scale * $old_height);
        }

        return (object)[
            'src'  => $image,
            'size' => (object)[
                'width'  => $width,
                'height' => $height
            ],
            'type' => $type
        ];
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return string|null
     */
    public function getCMSContent(array $params, Smarty_Internal_Template $smarty): ?string
    {
        $linkID = (int)($params['kLink'] ?? 0);
        if ($linkID <= 0) {
            return null;
        }
        $link    = Shop::Container()->getLinkService()->getLinkByID($linkID);
        $content = $link?->getContent();
        if (!isset($params['assign'])) {
            return $content;
        }
        $smarty->assign($params['assign'], $content);

        return null;
    }

    /**
     * @param array                    $params - variationen, maxVariationCount, maxWerteCount
     * @param Smarty_Internal_Template $smarty
     * @return int|void
     * 0: no listable variations
     * 1: normal listable variations
     * 2: only child listable variations
     */
    public function hasOnlyListableVariations(array $params, Smarty_Internal_Template $smarty)
    {
        if (!isset($params['artikel']->Variationen)) {
            if (isset($params['assign'])) {
                $smarty->assign($params['assign'], 0);

                return;
            }

            return 0;
        }

        $maxVariationCount = (int)($params['maxVariationCount'] ?? 1);
        $maxWerteCount     = (int)($params['maxWerteCount'] ?? 3);
        $variationCheck    = static function ($variations, $maxVariationCount, $maxValueCount): bool {
            $result   = true;
            $varCount = \is_array($variations) ? \count($variations) : 0;

            if ($varCount > 0 && $varCount <= $maxVariationCount) {
                foreach ($variations as $oVariation) {
                    if ($oVariation->cTyp !== 'SELECTBOX'
                        && (!\in_array($oVariation->cTyp, ['TEXTSWATCHES', 'IMGSWATCHES', 'RADIO'], true)
                            || \count($oVariation->Werte) > $maxValueCount)) {
                        $result = false;
                        break;
                    }
                }
            } else {
                $result = false;
            }

            return $result;
        };

        $result = $variationCheck($params['artikel']->Variationen, $maxVariationCount, $maxWerteCount) ? 1 : 0;
        if ($result === 0 && $params['artikel']->kVaterArtikel > 0) {
            // Hat das Kind evtl. mehr Variationen als der Vater?
            $result = $variationCheck($params['artikel']->oVariationenNurKind_arr, $maxVariationCount, $maxWerteCount)
                ? 2
                : 0;
        }
        if (!isset($params['assign'])) {
            return $result;
        }
        $smarty->assign($params['assign'], $result);
    }

    /**
     * Input: ['ger' => 'Titel', 'eng' => 'Title']
     *
     * @param mixed        $mixed
     * @param string|null  $to - locale
     * @return null|string
     */
    public function getTranslation(mixed $mixed, ?string $to = null): ?string
    {
        $to = $to ?? Shop::getLanguageCode();
        if ($this->hasTranslation($mixed, $to)) {
            return \is_string($mixed) ? $mixed : $mixed[$to];
        }

        return null;
    }

    /**
     * Has any translation
     *
     * @param mixed        $mixed
     * @param string|null  $to - locale
     * @return bool
     */
    public function hasTranslation(mixed $mixed, ?string $to = null): bool
    {
        return \is_string($mixed) || isset($mixed[$to ?? Shop::getLanguageCode()]);
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return string
     */
    public function captchaMarkup(array $params, Smarty_Internal_Template $smarty): string
    {
        if (isset($params['getBody']) && $params['getBody']) {
            return Shop::Container()->getCaptchaService()->getBodyMarkup($smarty);
        }

        return Shop::Container()->getCaptchaService()->getHeadMarkup($smarty);
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return array|null|void
     */
    public function getStates(array $params, Smarty_Internal_Template $smarty)
    {
        $regions = Staat::getRegions($params['cIso'] ?? '');
        if (!isset($params['assign'])) {
            return $regions;
        }
        $smarty->assign($params['assign'], $regions);
    }

    /**
     * @param array $params
     * @return int
     */
    public function getDecimalLength(array $params): int
    {
        $portion = \strrchr(\str_replace(',', '.', $params['quantity']), '.');
        if ($portion === false) {
            $portion = '';
        }

        return \max(\strlen($portion) - 1, 0);
    }

    /**
     * prepares a string optimized for SEO
     * @param string $optStr
     * @return string SEO optimized String
     */
    public function seofy(string $optStr = ''): string
    {
        return \str_replace('/', '-', Seo::sanitizeSeoSlug($optStr));
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return void
     */
    public function getUploaderLang(array $params, Smarty_Internal_Template $smarty): void
    {
        if (!isset($params['assign'], $params['iso'])) {
            return;
        }
        $available = [
            'ar', 'az', 'bg', 'ca', 'cr', 'cs', 'da', 'de', 'el', 'es', 'et', 'fa', 'fi', 'fr', 'gl', 'he', 'hu', 'id',
            'it', 'ja', 'ka', 'kr', 'kz', 'lt', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sv', 'th', 'tr', 'uk',
            'uz', 'vi', 'zh'
        ];

        $smarty->assign($params['assign'], \in_array($params['iso'], $available, true) ? $params['iso'] : 'LANG');
    }

    /**
     * @param array                    $params
     * @param Smarty_Internal_Template $smarty
     * @return void
     */
    public function getCountry(array $params, Smarty_Internal_Template $smarty): void
    {
        if (!isset($params['assign'], $params['iso'])) {
            return;
        }
        $smarty->assign($params['assign'], Shop::Container()->getCountryService()->getCountry($params['iso']));
    }

    /**
     * @param array $params
     * @return string
     */
    public function sanitizeTitle(array $params): string
    {
        return \htmlspecialchars($params['title'] ?? '', \ENT_COMPAT, \JTL_CHARSET, false);
    }

    /**
     * format price strings to have a '.' to indicate decimal separator. (https://schema.org/price)
     * @param String $price
     * @return String
     */
    public function formatForMicrodata(string $price = ''): string
    {
        $currSep = Frontend::getCurrency()->getDecimalSeparator();
        $currTho = Frontend::getCurrency()->getThousandsSeparator();
        $price   = \html_entity_decode($price, \ENT_COMPAT, \JTL_CHARSET);

        \preg_match('/\d+(?:[' . $currTho . ']\d{3})*(?:[' . $currSep . ']\d+)?/', $price, $extractedPrice);

        return \sprintf('%.2f', \str_replace($currSep, '.', \str_replace($currTho, '', ($extractedPrice[0]))));
    }
}
