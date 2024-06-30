<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 * @global Plugin $oPlugin
 * @global JTLSmarty $smarty
 */
$results  = null;
$type     = isset($_REQUEST['validate']) ? $_REQUEST['validate'] : null;
$security = isset($_REQUEST['security']) ? $_REQUEST['security'] : null;

$module = "kPlugin_{$oPlugin->kPlugin}_paypal";
$method = "/" . str_replace('frontend', 'paymentmethod', $oPlugin->cFrontendPfad);

$config = Shop::getSettings([CONF_KAUFABWICKLUNG]);

if ($type) {
    switch ($type) {
        case 'basic':
            $module = "{$module}{$type}";
            require_once $method . 'class/PayPalBasic.class.php';
            $payPal  = new PayPalBasic($oPlugin->oPluginZahlungsmethodeAssoc_arr[$module]->cModulId);
            $results = $payPal->test();
            break;
        case 'express':
            $module = "{$module}{$type}";
            require_once $method . 'class/PayPalExpress.class.php';
            $payPal  = new PayPalExpress($oPlugin->oPluginZahlungsmethodeAssoc_arr[$module]->cModulId);
            $results = $payPal->test();
            break;
        case 'plus':
            $module = "{$module}{$type}";
            require_once $method . 'class/PayPalPlus.class.php';
            $payPal  = new PayPalPlus($oPlugin->oPluginZahlungsmethodeAssoc_arr[$module]->cModulId);
            $results = $payPal->test();
            break;
    }

    if ($payPal) {
        $results['modus']  = $payPal->getModus();
        $results['linked'] = PayPalHelper::isLinked($payPal->getPaymentId());

        if (!preg_match('/^[\w\-_+\\\\\/]+$/m', $config['kaufabwicklung']['bestellabschluss_bestellnummer_praefix']) ||
            !preg_match('/^[\w\-_+\\\\\/]+$/m', $config['kaufabwicklung']['bestellabschluss_bestellnummer_suffix'])
        ) {
            $results['invoice_number'] = 'Ung&uuml;ltige Zeichen in der Bestellnummer';
        }
    }

    $results['type'] = $type;
    die(json_encode($results));
}

require_once $method . 'class/PayPal.helper.class.php';

PayPalHelper::copyCredentials($oPlugin->oPluginEinstellungAssoc_arr, $module, 'basic', 'express',
    ['api_live_sandbox', 'api_user', 'api_pass', 'api_signatur', 'api_sandbox_user', 'api_sandbox_pass', 'api_sandbox_signatur']
);

$smarty->assign('results', $results)
       ->assign('post_url', Shop::getURL(true) . '/' . PFAD_ADMIN . 'plugin.php?kPlugin=' . $oPlugin->kPlugin . '')
       ->display($oPlugin->cAdminmenuPfad . 'templates/infos.tpl');
