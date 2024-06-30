<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 * @global Plugin $oPlugin
 */

// HOOK_BESTELLABSCHLUSS_INC_WARENKORBINDB

require_once PFAD_ROOT . PFAD_INCLUDES_MODULES . 'PaymentMethod.class.php';

if ($_SESSION['Zahlungsart']->cModulId !== 'kPlugin_' . $oPlugin->kPlugin . '_paypalexpress') {
    return;
}

/** @var PayPalExpress $payMethod */
$payMethod = PaymentMethod::create($_SESSION['Zahlungsart']->cModulId);
if (($payMethod instanceof PayPalExpress) && $payMethod->checkOvercharge()) {
    $payMethod->zahlungsprozess();
}
