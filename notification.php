<?php

/**
 * Express Pay Notification
 * 
 * @package    paygw_expresspay
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
require_once('./../../../config.php');
//require_once($CFG->dirroot . '/payment/gateway/mollie/thirdparty/Mollie/vendor/autoload.php');

use paygw_expresspay\expresspay_helper;
use core_payment\helper;

$params = [
    'data' => required_param('Data', PARAM_RAW),
    'signature' => optional_param('Signature', null, PARAM_TEXT)
];

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/payment/gateway/expresspay/notification.php');
$PAGE->set_pagelayout('admin');
$pagetitle = 'EXPRESSPAY NOTIFICATION';
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

// Instant, we want this AS QUICK as we can.
try {
    $notifObj = json_decode($params['data']);
    if ($notifObj->CmdType == 1){
        header("HTTP/1.1 200 OK");
        exit(1);
    }
    // Callback is provided with internal record ID to match OUR record.
    $transactionrecord = $DB->get_record('paygw_expresspay', [
        'id' => $notifObj->AccountNo,
        'invoiceid' => $notifObj->InvoiceNo
    ], '*', MUST_EXIST);
    // And sycnhronize status.

    $config = (object) helper::get_gateway_configuration($transactionrecord->component, 
    $transactionrecord->paymentarea, 
    $transactionrecord->itemid, 'expresspay');

    $expresspay_helper = new expresspay_helper($config);

    $expresspay_helper->notify($params['data'], $params['signature'], $transactionrecord);

    header("HTTP/1.1 200 OK");
} catch (\Exception $e) {
    // NON HTTP-200.
    header("HTTP/1.1 403 Forbiden");
    echo $e->getMessage();
}
exit;
