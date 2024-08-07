<?php

/*
 * This is the IPN file remotely accessed by amazon for notifications about payments.
 */
require_once(__DIR__ . '/lib/lpa_includes.php');
$oPlugin = Plugin::getPluginById('s360_amazon_lpa_shop4');
require_once(PFAD_ROOT . PFAD_INCLUDES . "tools.Global.php");
require_once($oPlugin->cFrontendPfad . 'lib/lpa_defines.php');
require_once($oPlugin->cFrontendPfad . 'lib/lpa_utils.php');
require_once($oPlugin->cFrontendPfad . 'lib/class.LPAStatusHandler.php');
require_once($oPlugin->cFrontendPfad . 'lib/class.LPAAdapter.php');
require_once($oPlugin->cFrontendPfad . 'lib/amazon-pay-sdk-php-3.4.1/AmazonPay/IpnHandler.php');

if (!$oPlugin || $oPlugin->kPlugin == 0 || $oPlugin->oPluginEinstellungAssoc_arr[S360_LPA_CONFKEY_ADVANCED_USEIPN] === '0') {
    header("HTTP/1.1 503 Service Unavailable");
    exit(0);
}

if (!function_exists('lpa_getallheaders')) {
    /*
     * Workaround function if we run on php variants without getallheaders (i.e. FastCGI)
     */
    function lpa_getallheaders() {
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) === 'HTTP_') {
                $headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
            } else {
                $headers[strtolower($name)] = $value;
            }
        }
        return $headers;
    }

}

if (isset($_GET['lpacheck'])) {
    $query = $_SERVER['PHP_SELF'];
    $path = pathinfo($query);
    header("HTTP/1.1 200 OK");
    echo "IPN is reachable. (" . $path['dirname'] . ")";
    exit(0);
}

try {


    $headers = lpa_getallheaders();
    $body = file_get_contents('php://input');
    Jtllog::writeLog("LPA: LPA-IPN: IPN empfangen:\nHeaders: " . print_r(htmlspecialchars(implode(', ', $headers), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE, "UTF-8"), true) . "\nBody: " . print_r(htmlspecialchars($body, ENT_COMPAT | ENT_HTML401 | ENT_IGNORE, "UTF-8"), true), JTLLOG_LEVEL_DEBUG);


    try {
        $ipnHandler = new \AmazonPay\IpnHandler($headers, $body);
        $notification = $ipnHandler->toArray();
        Jtllog::writeLog("LPA: LPA-IPN: IPN geparst: " . print_r(htmlspecialchars($notification, ENT_COMPAT | ENT_HTML401 | ENT_IGNORE, "UTF-8"), true), JTLLOG_LEVEL_DEBUG);
    } catch (Exception $ex) {
        Jtllog::writeLog('LPA: LPA-IPN-Fehler: Invalide Nachricht empfangen: ' . $ex->getMessage(), JTLLOG_LEVEL_ERROR);
        header("HTTP/1.1 400 Bad Request");
        exit(0);
    }


    $handler = new LPAStatusHandler(S360_LPA_ADAPTER_MODE_BACKEND);
    $adapter = new LPAAdapter(S360_LPA_ADAPTER_MODE_BACKEND);
    $notificationType = $notification['NotificationType'];
    $result = null;
    /*
     * Acting according to best practices: on receipt of an IPN for an object, use the respected getX-Call to get the details.
     */
    switch ($notificationType) {
        case 'OrderReferenceNotification':
            $orid = $notification['OrderReference']['AmazonOrderReferenceId'];
            Jtllog::writeLog("LPA: LPA-IPN: Notification f�r Order {$orid} empfangen.", JTLLOG_LEVEL_DEBUG);
            if ($orid === S360_LPA_TEST_IPN_ID) {
                Jtllog::writeLog("LPA: LPA-IPN: {$orid} wurde als Test-IPN erkannt. Empfang OK.", JTLLOG_LEVEL_NOTICE);
                header("HTTP/1.1 200 OK");
                exit(0);
            }
            $details = $adapter->getRemoteOrderReferenceDetails($orid);
            $result = $handler->handleOrderReferenceDetails($details);
            break;
        case 'AuthorizationNotification':
        case 'PaymentAuthorize':
            $authid = $notification['AuthorizationDetails']['AmazonAuthorizationId'];
            Jtllog::writeLog("LPA: LPA-IPN: Notification f�r Authorization {$authid} empfangen.", JTLLOG_LEVEL_DEBUG);
            if ($authid === S360_LPA_TEST_IPN_ID) {
                Jtllog::writeLog("LPA: LPA-IPN: {$authid} wurde als Test-IPN erkannt. Empfang OK.", JTLLOG_LEVEL_NOTICE);
                header("HTTP/1.1 200 OK");
                exit(0);
            }
            $details = $adapter->getRemoteAuthorizationDetails($authid);
            $result = $handler->handleAuthorizationDetails($details);
            break;
        case 'CaptureNotification':
        case 'PaymentCapture':
            $capid = $notification['CaptureDetails']['AmazonCaptureId'];
            Jtllog::writeLog("LPA: LPA-IPN: Notification f�r Capture {$capid} empfangen.", JTLLOG_LEVEL_DEBUG);
            if ($capid === S360_LPA_TEST_IPN_ID) {
                Jtllog::writeLog("LPA: LPA-IPN: {$capid} wurde als Test-IPN erkannt. Empfang OK.", JTLLOG_LEVEL_NOTICE);
                header("HTTP/1.1 200 OK");
                exit(0);
            }
            $details = $adapter->getRemoteCaptureDetails($capid);
            $result = $handler->handleCaptureDetails($details);
            break;
        case 'RefundNotification':
        case 'PaymentRefund':
            $refid = $notification['RefundDetails']['AmazonRefundId'];
            Jtllog::writeLog("LPA: LPA-IPN: Notification f�r Refund {$refid} empfangen.", JTLLOG_LEVEL_DEBUG);
            if ($refid === S360_LPA_TEST_IPN_ID) {
                Jtllog::writeLog("LPA: LPA-IPN: {$refid} wurde als Test-IPN erkannt. Empfang OK.", JTLLOG_LEVEL_NOTICE);
                header("HTTP/1.1 200 OK");
                exit(0);
            }
            $details = $adapter->getRemoteRefundDetails($refid);
            $result = $handler->handleRefundDetails($details);
            break;
        default:
            Jtllog::writeLog('LPA: LPA-IPN: Unerwarteten NotificationType empfangen: ' . $notificationType .', Inhalt: '.print_r(htmlspecialchars($notification, ENT_COMPAT | ENT_HTML401 | ENT_IGNORE, "UTF-8"), true), JTLLOG_LEVEL_NOTICE);
            header("HTTP/1.1 400 Bad Request");
            exit(0);
            break;
    }
    if(!empty($result)) {
        // in normal use cases $result should be empty (=== null). if it is not, an error has occured!
        if($result === S360_LPA_ERROR_UNKNOWN_ID) {
            /* 
             * Unknown payment object ID - we return a 500 error so amazon retries this later 
             * (due to timing and race conditions or very slow databases this might occur occasionally!)
             */
            header("HTTP/1.1 503 Service Unavailable");
            exit(0);
        }
    }
} catch (Exception $ex) {
    Jtllog::writeLog('LPA: LPA-IPN-Fehler: ' . $ex->getMessage(), JTLLOG_LEVEL_ERROR);
    header("HTTP/1.1 503 Service Unavailable");
    exit(0);
}