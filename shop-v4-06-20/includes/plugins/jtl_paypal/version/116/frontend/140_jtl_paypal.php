<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 * @global Plugin $oPlugin
 * @global JTLSmarty $smarty
 */
$pageType = Shop::getPageType();

if ($pageType === PAGE_WARENKORB || ($pageType === PAGE_ARTIKEL && $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_article'] === 'Y')) {
    require_once str_replace('frontend', 'paymentmethod', $oPlugin->cFrontendPfad) . '/class/PayPalExpress.class.php';

    $payPalExpress    = new PayPalExpress();
    $pqMethodCart     = $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_button_method'];
    $pqSelectorCart   = $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_button_selector'];
    $pqMethodArticle  = $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_article_method'];
    $pqSeletorArticle = $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_article_selector'];
    $articleBtnType   = ($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_article_button_type'] === 'silver') ? '-alt' : '';
    $cartBtnType      = ($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_button_type'] === 'silver') ? '-alt' : '';
    $articleBtnSize   = (isset($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_article_button_size']))
        ? $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_article_button_size']
        : 'medium';
    $cartBtnSize      = (isset($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_button_size']))
        ? $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_button_size']
        : 'medium';
    $allowedISO       = ['de', 'en', 'es', 'fr', 'nl'];
    $iso              = StringHandler::convertISO2ISO639($_SESSION['cISOSprache']);
    $iso              = (!in_array($iso, $allowedISO)) ? 'de' : $iso;
    $ppArticle        = $oPlugin->cFrontendPfadURLSSL . 'images/buttons/' . $iso . '/checkout-logo-' . $articleBtnSize . $articleBtnType . '-' . $iso . '.png';
    $ppCart           = $oPlugin->cFrontendPfadURLSSL . 'images/buttons/' . $iso . '/checkout-logo-' . $cartBtnSize . $cartBtnType . '-' . $iso . '.png';

    if ($pageType === PAGE_WARENKORB && $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_button'] === 'Y') {
        $oArtikel_arr = PayPalHelper::getProducts();
        if ($payPalExpress->zahlungErlaubt($oArtikel_arr)) {
            $link = PayPalHelper::getLinkByName($oPlugin, 'PayPalExpress');
            if ($link !== null) {
                pq($pqSelectorCart)->$pqMethodCart(
                    '<a href="index.php?s=' . $link->kLink . '&jtl_paypal_checkout_cart=1" class="paypalexpress btn-ppe-cart">' .
                    '  <img src="' . $ppCart . '" alt="' . $oPlugin->cName . '" />' .
                    '</a>'
                );
            }
        }
    } elseif ($pageType === PAGE_ARTIKEL && $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_article'] === 'Y') {
        $oArtikel = $smarty->get_template_vars('Artikel');
        if ($payPalExpress->zahlungErlaubt([$oArtikel])) {
            pq($pqSeletorArticle)->$pqMethodArticle(
                '<button name="jtl_paypal_redirect" type="submit" value="2" class="paypalexpress btn-ppe-article">' .
                '  <img src="' . $ppArticle . '" alt="' . $oPlugin->cName . '" />' .
                '</button>'
            );
        }
    }
}

if ($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_checkout'] === 'Y'
    && $pageType === PAGE_BESTELLVORGANG && $GLOBALS['step'] === 'accountwahl') {
    require_once str_replace('frontend', 'paymentmethod', $oPlugin->cFrontendPfad) . '/class/PayPalExpress.class.php';

    $payPalExpress    = new PayPalExpress();
    $pqMethodCart     = $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_checkout_method'];
    $pqSelectorCart   = $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_checkout_selector'];
    $btnType          = ($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_checkout_button_type'] === 'silver') ? '-alt' : '';
    $btnSize          = (isset($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_checkout_button_size']))
        ? $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_checkout_button_size']
        : 'medium';
    $allowedISO       = ['de', 'en', 'es', 'fr', 'nl'];
    $iso              = StringHandler::convertISO2ISO639($_SESSION['cISOSprache']);
    $iso              = (!in_array($iso, $allowedISO)) ? 'de' : $iso;
    $ppCheckout       = $oPlugin->cFrontendPfadURLSSL . 'images/buttons/' . $iso . '/checkout-logo-' . $btnSize . $btnType . '-' . $iso . '.png';

    $oArtikel_arr = PayPalHelper::getProducts();
    if ($payPalExpress->zahlungErlaubt($oArtikel_arr)) {
        $link = PayPalHelper::getLinkByName($oPlugin, 'PayPalExpress');
        if ($link !== null) {
            pq($pqSelectorCart)->$pqMethodCart(
                '<a href="index.php?s=' . $link->kLink . '&jtl_paypal_checkout_cart=1" class="paypalexpress btn-ppe-checkout">' .
                '  <img src="' . $ppCheckout . '" alt="' . $oPlugin->cName . '" />' .
                '</a>'
            );
        }
    }
}

if ($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_popup'] === 'Y'
    && isset($_SESSION["Warenkorb"])
    && $_SESSION["Warenkorb"]->istBestellungMoeglich() == 10
) {
    require_once str_replace('frontend', 'paymentmethod', $oPlugin->cFrontendPfad) . '/class/PayPalExpress.class.php';

    $payPalExpress    = new PayPalExpress();
    $pqMethodCart     = $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_popup_method'];
    $pqSelectorCart   = $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_popup_selector'];
    $btnType          = ($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_popup_button_type'] === 'silver') ? '-alt' : '';
    $btnSize          = (isset($oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_popup_button_size']))
        ? $oPlugin->oPluginEinstellungAssoc_arr['jtl_paypal_express_cart_popup_button_size']
        : 'medium';
    $allowedISO       = ['de', 'en', 'es', 'fr', 'nl'];
    $iso              = StringHandler::convertISO2ISO639($_SESSION['cISOSprache']);
    $iso              = (!in_array($iso, $allowedISO)) ? 'de' : $iso;
    $ppCheckout       = $oPlugin->cFrontendPfadURLSSL . 'images/buttons/' . $iso . '/checkout-logo-' . $btnSize . $btnType . '-' . $iso . '.png';

    $oArtikel_arr = PayPalHelper::getProducts();
    if ($payPalExpress->zahlungErlaubt($oArtikel_arr)) {
        $link = PayPalHelper::getLinkByName($oPlugin, 'PayPalExpress');
        if ($link !== null) {
            $smarty->assign('link', $link);
            $smarty->assign('oPlugin', $oPlugin);
            $smarty->assign('ppCheckout', $ppCheckout);
            $smarty->assign('pqMethodCart', $pqMethodCart);
            $smarty->assign('pqSelectorCart', $pqSelectorCart);
            $tpl = $smarty->fetch($oPlugin->cFrontendPfad . 'template/inc_basket.tpl');
            pq('body')->append($tpl);
        }
    }
}

if ($pageType === PAGE_BESTELLVORGANG && $GLOBALS['step'] === 'Bestaetigung') {
    if ($_SESSION['Zahlungsart']->cModulId === 'kPlugin_' . $oPlugin->kPlugin . '_paypalexpress') {
        /** @var PayPalExpress $payMethod */
        $payMethod = PaymentMethod::create($_SESSION['Zahlungsart']->cModulId);
        if (($payMethod instanceof PayPalExpress) && $payMethod->checkOvercharge()) {
            $link = PayPalHelper::getLinkByName($oPlugin, 'PayPalExpress');
            $smarty->assign('pp_psd2overcharge', true)
                   ->assign('pp_psd2overcharge_title', $payMethod->oPlugin->oPluginSprachvariableAssoc_arr['jtl_paypal_payment_title_psd2overcharge'])
                   ->assign('pp_psd2overcharge_desc', $payMethod->oPlugin->oPluginSprachvariableAssoc_arr['jtl_paypal_payment_description_psd2overcharge'])
                   ->assign('pp_psd2overcharge_link', ($link !== null) ? URL_SHOP . '/index.php?s=' . $link->kLink . '&jtl_paypal_redirect=1' : '');
        }
    }

    $tpl = $smarty->fetch($oPlugin->cFrontendPfad . 'template/inc_order_confirmation.tpl');
    pq('body')->append($tpl);
}

require_once $oPlugin->cAdminmenuPfad . 'PPCBannerConfig.php';
require_once $oPlugin->cFrontendPfad . 'PPCBanner.php';
if ($pageType === PAGE_ARTIKEL) {
    (new PPCBanner($oPlugin))->show(PPCBannerConfig::POSITION_PAGE_PRODUCT, $smarty);
} elseif ($pageType === PAGE_WARENKORB) {
    (new PPCBanner($oPlugin))->show(PPCBannerConfig::POSITION_PAGE_CART, $smarty);
} elseif ($pageType === PAGE_BESTELLVORGANG) {
    (new PPCBanner($oPlugin))->show(PPCBannerConfig::POSITION_PAGE_PAYMENT, $smarty);
} elseif (isset($_SESSION['Warenkorb']) && $_SESSION['Warenkorb']->istBestellungMoeglich() === 10) {
    (new PPCBanner($oPlugin))->show(PPCBannerConfig::POSITION_POPUP_CART, $smarty);
}
