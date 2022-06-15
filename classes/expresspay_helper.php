<?php

/**
 * Contains helper class to work with ExpressPay REST API.
 *
 * @package    core_payment
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_expresspay;

use curl;
use core_payment\helper;
use Exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

class expresspay_helper
{
    /**
     * @var string The base API URL
     */
    private string $baseurl;

    /**
     * @var string Service secret word
     */
    private string $secret;

    /**
     * @var string Service Token
     */
    private string $token;

    /**
     * @var string Service Number
     */
    private $serviceno;

    /**
     * @var bool Use signature for notification
     */
    private bool $useNotifSignature;

    /**
     * @var string Secret word for notification
     */
    private string $notifSecret;

    /**
     * @var string It is allowed to change the name of the payer
     */
    private $isNameEditable;

    /**
     * @var string Allowed to change the payer's address
     */
    private $isAddressEditable;

    /**
     * @var string Allowed to change the amount of payment
     */
    private $isAmountEditable;

    /**
     * @var string Enable email notification
     */
    private $emailNotification;

    /**
     * @var string Enable sms notification
     */
    private $smsNotification;

    /**
     * helper constructor.
     *
     * @param object $config Config for Express Pay Payment Gateway
     */
    public function __construct(
        object $config
    ) {
        $this->serviceno = $config->serviceno;
        $this->token = $config->token;
        $this->secret = $config->secretwordforsigninginvoices;
        $this->useNotifSignature = $config->usedigitallysignnotifications;
        $this->notifSecret = $config->secretwordforsigningnotifications;
        $this->baseurl = $config->testmode ? 'https://sandbox-api.express-pay.by/v2/invoices' : 'https://api.express-pay.by/v2/invoices';
        $this->isNameEditable = $config->isnameeditable;
        $this->isAddressEditable = $config->isaddresseditable;
        $this->isAmountEditable = $config->isamounteditable;
        $this->emailNotification = $config->sendemail;
        $this->smsNotification = $config->sendsms;
    }

    /**
     * Captures an authorized payment, by ID.
     *
     * @param string $authorizationid The PayPal-generated ID for the authorized payment to capture.
     * @param float $amount The amount to capture.
     * @param string $currency The currency code for the amount.
     * @param bool $final Indicates whether this is the final captures against the authorized payment.
     * @return array|null Formatted API response.
     */
    public function place_invoice(float $amount, string $currency, int $accountId, string $description): ?array
    {
        global $USER, $DB, $CFG;

        $params = array(
            "ServiceId" => $this->serviceno,
            "AccountNo" => $accountId,
            "Amount" => $amount,
            "Currency" => $this->get_currency_number($currency),
            "ReturnType" => "json",
            "Surname" => $USER->lastname,
            "FirstName" => $USER->firstname,
            "City" => $USER->city,
            "ReturnUrl" => $CFG->wwwroot,
            "FailUrl" => $CFG->wwwroot,
            "Info" => $description,
            "IsNameEditable" => $this->isNameEditable,
            "IsAddressEditable" => $this->isAddressEditable,
            "IsAmountEditable" => $this->isAmountEditable,
            "EmailNotification" => (bool)$this->emailNotification ? $USER->email : null,
            "SmsPhone" => (bool)$this->smsNotification ? $USER->phone1 ?? $USER->phone2 : null
        );

        $params["Signature"] = $this->get_signature($params, 'add-invoice-v2');

        return $this->send_post($params);
    }

    /**
     * Request for ExpressPay POST.
     *
     * @return array
     */
    private function send_post(array $requestParams): array
    {
        $location = $this->baseurl;

        $options = [
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => 30,
            'CURLOPT_HTTP_VERSION' => CURL_HTTP_VERSION_1_1,
            'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
        ];

        $curl = new curl();
        $result = $curl->post($location, http_build_query($requestParams, '', '&'), $options);

        $result = json_decode($result, true);

        return $result;
    }


    /**
     * 
     * Calculate digital signature for request
     * 
     * @param array $requestParams Request Params
     * @param string $method Method API
     * 
     * @return string Signature
     */
    private function get_signature(array $requestParams, string $method): string
    {
        $normalizedParams = array_change_key_case($requestParams, CASE_LOWER);
        $mapping = array(
            "add-invoice" => array(
                "token",
                "accountno",
                "amount",
                "currency",
                "expiration",
                "info",
                "surname",
                "firstname",
                "patronymic",
                "city",
                "street",
                "house",
                "building",
                "apartment",
                "isnameeditable",
                "isaddresseditable",
                "isamounteditable",
                "emailnotification",
                "returninvoiceurl"
            ),
            "get-details-invoice" => array(
                "token",
                "invoiceno",
                "returninvoiceurl"
            ),
            "cancel-invoice" => array(
                "token",
                "invoiceno"
            ),
            "status-invoice" => array(
                "token",
                "invoiceno"
            ),
            "get-list-invoices" => array(
                "token",
                "from",
                "to",
                "accountno",
                "status"
            ),
            "get-list-payments" => array(
                "token",
                "from",
                "to",
                "accountno"
            ),
            "get-details-payment" => array(
                "token",
                "paymentno"
            ),
            "add-card-invoice"  =>  array(
                "token",
                "accountno",
                "expiration",
                "amount",
                "currency",
                "info",
                "returnurl",
                "failurl",
                "language",
                "pageview",
                "sessiontimeoutsecs",
                "expirationdate",
                "returninvoiceurl"
            ),
            "card-invoice-form"  =>  array(
                "token",
                "cardinvoiceno"
            ),
            "status-card-invoice" => array(
                "token",
                "cardinvoiceno",
                "language"
            ),
            "reverse-card-invoice" => array(
                "token",
                "cardinvoiceno"
            ),
            "get-qr-code"          => array(
                "token",
                "invoiceid",
                "viewtype",
                "imagewidth",
                "imageheight"
            ),
            "add-web-invoice" => array(
                "token",
                "serviceid",
                "accountno",
                "amount",
                "currency",
                "expiration",
                "info",
                "surname",
                "firstname",
                "patronymic",
                "city",
                "street",
                "house",
                "building",
                "apartment",
                "isnameeditable",
                "isaddresseditable",
                "isamounteditable",
                "emailnotification",
                "smsphone",
                "returntype",
                "returnurl",
                "failurl",
                "returninvoiceurl"
            ),
            "add-webcard-invoice" => array(
                "token",
                "serviceid",
                "accountno",
                "expiration",
                "amount",
                "currency",
                "info",
                "returnurl",
                "failurl",
                "language",
                "sessiontimeoutsecs",
                "expirationdate",
                "returntype",
                "returninvoiceurl"
            ),
            "get-balance" => array(
                "token",
                "accountno"
            ),
            "add-invoice-v2" => array(
                "token",
                "serviceid",
                "accountno",
                "amount",
                "currency",
                "expiration",
                "info",
                "surname",
                "firstname",
                "patronymic",
                "city",
                "street",
                "house",
                "building",
                "apartment",
                "isnameeditable",
                "isaddresseditable",
                "isamounteditable",
                "emailnotification",
                "smsphone",
                "returntype",
                "returnurl",
                "failurl",
            ),
        );

        $apiMethod = $mapping[$method];

        $result = $this->token;
        foreach ($apiMethod as $item) {
            $result .= $normalizedParams[$item] ?? null;
        }
        $hash = strtoupper(hash_hmac('sha1', $result, $this->secret));
        return $hash;
    }

    /**
     * Get Currency Number IBAN.
     * 
     * @param string $currency Currency code
     *
     * @return int
     */
    private function get_currency_number(string $currency): int
    {
        $currencyNumber = array(
            "USD" => 840,
            "EUR" => 978,
            "RUB" => 933,
            "BYN" => 933
        );

        return $currencyNumber[$currency] ?? 933;
    }

    /**
     * Update record in DB and delivery cource if signature is correct
     * 
     * @param string $notifJson notification JSON
     * @param string $signature notification digital signature
     * @param string $notifJson Record object
     * 
     */
    public function notify(string $notifJson, ?string $signature, object $transactionrecord)
    {
        if ($this->useNotifSignature && $this->check_notify_signature($signature, $notifJson)) {
            $notifObj = json_decode($notifJson);
            if ($notifObj->CmdType = 3) {
                if ($notifObj->Status = 3 || $notifObj->Status = 6) {
                    $this->delivery_cource($transactionrecord);
                }
            }
        } else if (!$this->useNotifSignature) {
            $this->delivery_cource($transactionrecord);
        } else {
            throw new Exception("Incorrect digital signature!");
        }
    }

    /**
     * Check Notification digital signature
     * 
     * @param string $signature notification digital signature
     * @param string $data notification JSON
     * 
     * @return bool
     * 
     */
    private function check_notify_signature(string $signature, string $data): bool
    {
        $hash = NULL;

        $secretWord = trim($this->notifSecret);

        if (empty($secretWord))
            $hash = strtoupper(hash_hmac('sha1', $data, ""));
        else
            $hash = strtoupper(hash_hmac('sha1', $data, $secretWord));
        return $hash == $signature;
    }

    /**
     * Delivery cource and update record in DB
     * 
     * @param object $transactionrecord Record object
     * 
     */
    private function delivery_cource(object $transactionrecord)
    {
        global $DB;

        $payable = helper::get_payable(
            $transactionrecord->component,
            $transactionrecord->paymentarea,
            $transactionrecord->itemid
        );
        $cost = helper::get_rounded_cost(
            $payable->get_amount(),
            $payable->get_currency(),
            helper::get_gateway_surcharge('expresspay')
        );
        $paymentid = helper::save_payment(
            $payable->get_account_id(),
            $transactionrecord->component,
            $transactionrecord->paymentarea,
            $transactionrecord->itemid,
            $transactionrecord->userid,
            $cost,
            $payable->get_currency(),
            'expresspay'
        );
        helper::deliver_order(
            $transactionrecord->component,
            $transactionrecord->paymentarea,
            $transactionrecord->itemid,
            $paymentid,
            $transactionrecord->userid
        );
        // Update state.
        $transactionrecord->status = "PAYED";
        $transactionrecord->timemodified = time();
        $transactionrecord->paymentid = $paymentid;
        $DB->update_record('paygw_expresspay', $transactionrecord);
    }
}
