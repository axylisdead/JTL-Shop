<?php

/**
 * Class LPALinkHelper
 *
 * Helper for everything regarding links.
 *
 */
require_once(__DIR__ . '/lpa_defines.php');

class LPALinkHelper {

    private static $id2file = array(
        S360_LPA_FRONTEND_LINK_CHECKOUT => 'lpa_checkout.php',
        S360_LPA_FRONTEND_LINK_COMPLETE => 'lpa_checkout_complete.php',
        S360_LPA_FRONTEND_LINK_CREATE => 'lpa_create.php',
        S360_LPA_FRONTEND_LINK_LOGIN => 'lpa_login.php',
        S360_LPA_FRONTEND_LINK_MERGE => 'lpa_merge.php',
        S360_LPA_FRONTEND_LINK_PROCESS_FAILURE => 'lpa_process_failure.php',
        S360_LPA_FRONTEND_LINK_PROCESS_SUCCESS => 'lpa_process_success.php'
    );

    public static function getFrontendLinkUrl($id, $seoPartOnly = false) {
        $file = self::$id2file[$id];
        $plugin = Plugin::getPluginById('s360_amazon_lpa_shop4');
        $kSprache =  Shop::getLanguage(false);

        if(empty($file) || null === $plugin) {
            return null;
        }

        $queryPrepared = 'SELECT * FROM tpluginlinkdatei tpl, tseo ts WHERE ts.cKey = "kLink" AND ts.kKey = tpl.kLink AND tpl.kPlugin = :kPlugin AND tpl.cDatei = :cDatei AND ts.kSprache = :kSprache';
        $result = Shop::DB()->executeQueryPrepared($queryPrepared, ['kPlugin' => $plugin->kPlugin, 'cDatei' => $file, 'kSprache' => $kSprache], 1);

        if($seoPartOnly) {
            return $result->cSeo;
        }
        return Shop::getURL(true) . '/' . $result->cSeo;
    }
}



