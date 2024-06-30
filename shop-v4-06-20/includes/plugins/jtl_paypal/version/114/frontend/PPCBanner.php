<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 * @global Plugin $oPlugin
 */

require_once $oPlugin->cAdminmenuPfad . 'PPCBannerConfigItems.php';
require_once $oPlugin->cAdminmenuPfad . 'PPCBannerConfig.php';

/**
 * Class PPCBanner
 */
class PPCBanner
{
    private $plugin;

    /**
     * PPCBanner constructor.
     * @param Plugin $plugin
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param string    $position
     * @param JTLSmarty $smarty
     */
    public function show($position, $smarty)
    {
        $ppcConfig = PPCBannerConfig::instance($this->plugin, Shop::DB());
        if (!$ppcConfig->isPPCBannerActiv()) {
            return;
        }

        $settings    = $ppcConfig->getConfigItemsByPrefix(PPCBannerConfig::PREFIX);
        $apiClientID = $settings->get('api_client_id');
        if (empty($apiClientID)) {
            return;
        }
        $positions = [];
        foreach ([$position, PPCBannerConfig::POSITION_POPUP_CART] as $pos) {
            if ($ppcConfig->isPPCBannerActiv($pos)) {
                $positions[$pos] = [
                    'selector' => $settings->get($pos . '_query_selector'),
                    'method'   => $settings->get($pos . '_query_method', 'append')
                ];
            }
        }
        if (count($positions) === 0) {
            return;
        }
        /** @var Artikel $product */
        $product = $smarty->getTemplateVars('Artikel');
        try {
            $netto  = isset($_SESSION['Kundengruppe']) ? (int)$_SESSION['Kundengruppe']->nNettoPreise : 0;
            $banner = $smarty
                ->assign('settings', $settings)
                ->assign('positions', $positions)
                ->assign('apiNamespace', PPCBannerConfig::PREFIX . 'widget')
                ->assign('apiURL', $ppcConfig->getAPIUrl(
                    $apiClientID,
                    isset($_SESSION['Waehrung']) ? $_SESSION['Waehrung']->cISO : '')
                )
                ->assign('productPrice', $product instanceof Artikel && $product->inWarenkorbLegbar
                    ? $product->Preise->fVK[$netto]
                    : 0)
                ->assign('wkPrice', isset($_SESSION['Warenkorb'])
                    ? $_SESSION['Warenkorb']->gibGesamtsummeWaren(!$netto)
                    : 0)
                ->assign('netto', $netto)
                ->fetch($this->plugin->cFrontendPfad . 'template/ppc_banner.tpl');
        } catch (Exception $e) {
            Jtllog::writeLog($e->getMessage());

            return;
        }
        pq('body')->append($banner);
    }
}
