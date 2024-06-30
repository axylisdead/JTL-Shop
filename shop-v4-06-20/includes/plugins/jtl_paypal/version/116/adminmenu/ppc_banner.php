<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 * @global JTLSmarty $smarty
 * @global Plugin $oPlugin
 * @global $_adminMenu
 */

require_once $oPlugin->cAdminmenuPfad . 'PPCBannerConfigItems.php';
require_once $oPlugin->cAdminmenuPfad . 'PPCBannerConfig.php';

$config = PPCBannerConfig::instance($oPlugin, Shop::DB());

if ($_POST['task'] === 'savePPCSettings' && validateToken()) {
    $config->saveConfigItems(StringHandler::filterXSS($_POST['ppcSetting']), PPCBannerConfig::PREFIX);
}

$settings       = $config->getConfigItemsByPrefix(PPCBannerConfig::PREFIX);
$ppcAPIClientID = $settings->get('api_client_id');
$ppcAPIActive   = $ppcAPIClientID !== '';
$ppcPositions   = [
    PPCBannerConfig::POSITION_PAGE_PRODUCT => 'Anzeige auf der Artikeldetailseite',
    PPCBannerConfig::POSITION_PAGE_CART    => 'Anzeige im Warenkorb',
    PPCBannerConfig::POSITION_PAGE_PAYMENT => 'Anzeige im Bestellvorgang',
    PPCBannerConfig::POSITION_POPUP_CART   => 'Anzeige im Mini-Warenkorb',
];

$smarty
    ->assign('apiClientID', $ppcAPIClientID)
    ->assign('apiConficured', $ppcAPIActive)
    ->assign('apiURL', $ppcAPIActive ? $config->getAPIUrl($ppcAPIClientID) : '')
    ->assign('ppcSetting', $settings)
    ->assign('ppcPositions', $ppcPositions)
    ->assign('tplDefaults', json_encode((object)$config->getTplDefaults()))
    ->assign('kPlugin', $oPlugin->kPlugin)
    ->assign('kPluginAdminMenu', $_adminMenu->kPluginAdminMenu)
    ->display($oPlugin->cAdminmenuPfad . 'templates/ppc_banner.tpl');
