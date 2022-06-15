<?php

/**
 * This class contains a list of webservice functions related to the ExpressPay payment gateway.
 *
 *
 * @package    paygw_expresspay
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace paygw_expresspay\external;

use external_api;
use external_function_parameters;
use external_value;

use core_payment\helper;
use Exception;
use moodle_url;
use paygw_expresspay\expresspay_helper;

defined('MOODLE_INTERNAL') || die();



class create_payment extends external_api
{

    /**
     * Perform what needs to be done when a transaction is reported to be complete.
     * This function does not take cost as a parameter as we cannot rely on any provided value.
     *
     * @param string $component Name of the component that the itemid belongs to
     * @param string $paymentarea
     * @param int $itemid An internal identifier that is used by the component
     * @param string $description
     * @param string $paymentmethodid Payment method ID
     * @param int $bankid bank identifier|reserved for future use
     * @return array
     */
    public static function create_payment(
        string $component,
        string $paymentarea,
        int $itemid,
        string $description,
        string $paymentmethodid = null,
        int $bankid = null
    ): array {
        global $USER, $DB, $CFG;

        $params = self::validate_parameters(self::create_payment_parameters(), [
            'component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid,
            'description' => $description,
            'paymentmethodid' => $paymentmethodid,
            'bankid' => $bankid,
        ]);

        $config = (object) helper::get_gateway_configuration($component, $paymentarea, $itemid, 'expresspay');
        $payable = helper::get_payable($component, $paymentarea, $itemid);
        $currency = $payable->get_currency();
        $surcharge = helper::get_gateway_surcharge('expresspay');
        $amount = helper::get_rounded_cost($payable->get_amount(), $currency, $surcharge);

        try {
            $time = time();
            $record = (object)[
                'userid' => $USER->id,
                'component' => $component,
                'paymentarea' => $paymentarea,
                'itemid' => $itemid,
                'invoiceid' => -1,
                'status' => 'INIT',
                'statuscode' => 1,
                'testmode' => empty($config->testmode) ? 0 : 1,
                'timecreated' => $time,
                'timemodified' => $time
            ];
            
            $record->id = $DB->insert_record('paygw_expresspay', $record);

            $expresspay_helper = new expresspay_helper($config);

            $create_invoice_result = $expresspay_helper->place_invoice($amount, $currency, $record->id, $description);

            if (isset($create_invoice_result['Errors'])) {
                throw new Exception($create_invoice_result['Errors'][0]);
            }

            $redirecturl = $create_invoice_result['InvoiceUrl'];

            // Update record.
            $record->invoiceid = $create_invoice_result['ExpressPayInvoiceNo'];
            $record->id = $DB->update_record('paygw_expresspay', $record);

            /*$paymentid = helper::save_payment(
                $payable->get_account_id(),
                $component,
                $paymentarea,
                $itemid,
                (int) $USER->id,
                $amount,
                $currency,
                'expresspay'
            );

            // Store ExpressPay extra information.
            $record = new \stdClass();
            $record->paymentid = $paymentid;
            $record->pp_orderid = $create_invoice_result['ExpressPayInvoiceNo'];;

            $DB->insert_record('paygw_expresspay', $record);*/

            $success = true;
        } catch (\Exception $e) {
            debugging('Exception while trying to process payment: ' . $e->getMessage(), DEBUG_DEVELOPER);
            $success = false;
            $message = get_string('internalerror', 'paygw_expresspay') . $e->getMessage();
            $redirecturl = null;
        }

        return [
            'success' => $success,
            'message' => $message,
            'redirecturl' => $redirecturl,
        ];
    }

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function create_payment_parameters()
    {
        return new external_function_parameters([
            'component' => new external_value(PARAM_COMPONENT, 'The component name'),
            'paymentarea' => new external_value(PARAM_AREA, 'Payment area in the component'),
            'itemid' => new external_value(PARAM_INT, 'The item id in the context of the component area'),
            'description' => new external_value(PARAM_TEXT, 'Payment description'),
            'paymentmethodid' => new external_value(PARAM_TEXT, 'Payment method ID', VALUE_DEFAULT, null, NULL_ALLOWED),
            'bankid' => new external_value(PARAM_INT, 'Bank ID (reserved for future use)', VALUE_DEFAULT, null, NULL_ALLOWED),
        ]);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_function_parameters
     */
    public static function create_payment_returns()
    {
        return new external_function_parameters([
            'success' => new external_value(PARAM_BOOL, 'Whether everything was successful or not.'),
            'message' => new external_value(
                PARAM_RAW,
                'Message (usually the error message). Unused or not available if everything went well',
                VALUE_OPTIONAL
            ),
            'redirecturl' => new external_value(PARAM_RAW, 'Message (usually the error message).', VALUE_OPTIONAL),
        ]);
    }
}
