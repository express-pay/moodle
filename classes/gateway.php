<?php

/**
 * Contains class for ExpressPay payment gateway.
 *
 * @package    paygw_expresspay
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace paygw_expresspay;

/**
 * The gateway class for ExpressPay payment gateway.
 *
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use moodle_url;

class gateway extends \core_payment\gateway
{
    public static function get_supported_currencies(): array
    {
        // 3-character ISO-4217: https://en.wikipedia.org/wiki/ISO_4217#Active_codes.
        return ['EUR', 'USD', 'RUB'];
    }

    /**
     * Configuration form for the gateway instance
     *
     * Use $form->get_mform() to access the \MoodleQuickForm instance
     *
     * @param \core_payment\form\account_gateway $form
     */
    public static function add_configuration_to_gateway_form(\core_payment\form\account_gateway $form): void
    {
        global $CFG;

        $mform = $form->get_mform();

        $mform->addElement('advcheckbox', 'testmode', get_string('testmode', 'paygw_expresspay'), 0);
        $mform->addHelpButton('testmode', 'testmode', 'paygw_expresspay');

        $mform->addElement('text', 'serviceno', get_string('serviceno', 'paygw_expresspay'));
        $mform->setType('serviceno', PARAM_TEXT);
        $mform->addHelpButton('serviceno', 'serviceno', 'paygw_expresspay');

        $mform->addElement('text', 'token', get_string('token', 'paygw_expresspay'));
        $mform->setType('token', PARAM_TEXT);
        $mform->addHelpButton('token', 'token', 'paygw_expresspay');

        $mform->addElement('text', 'secretwordforsigninginvoices', get_string('secretwordforsigninginvoices', 'paygw_expresspay'));
        $mform->setType('secretwordforsigninginvoices', PARAM_TEXT);
        $mform->addHelpButton('secretwordforsigninginvoices', 'secretwordforsigninginvoices', 'paygw_expresspay');

        $mform->addElement('advcheckbox', 'usedigitallysignnotifications', get_string('usedigitallysignnotifications', 'paygw_expresspay'), 0);
        $mform->addHelpButton('usedigitallysignnotifications', 'usedigitallysignnotifications', 'paygw_expresspay');

        $mform->addElement('text', 'secretwordforsigningnotifications', get_string('secretwordforsigningnotifications', 'paygw_expresspay'));
        $mform->setType('secretwordforsigningnotifications', PARAM_TEXT);
        $mform->addHelpButton('secretwordforsigningnotifications', 'secretwordforsigningnotifications', 'paygw_expresspay');

        $notifurl = new moodle_url($CFG->wwwroot . '/payment/gateway/expresspay/notification.php');
        $attributes = array('value' => $notifurl->out(false), 'size'=>'70');
        $mform->addElement('text', 'notificationlink', get_string('notificationlink', 'paygw_expresspay'), $attributes);
        $mform->setType('notificationlink', PARAM_URL);
        $mform->addHelpButton('notificationlink', 'notificationlink', 'paygw_expresspay');

        $mform->addElement('advcheckbox', 'isnameeditable', get_string('isnameeditable', 'paygw_expresspay'), 0);
        $mform->addHelpButton('isnameeditable', 'isnameeditable', 'paygw_expresspay');

        $mform->addElement('advcheckbox', 'isaddresseditable', get_string('isaddresseditable', 'paygw_expresspay'), 0);
        $mform->addHelpButton('isaddresseditable', 'isaddresseditable', 'paygw_expresspay');

        $mform->addElement('advcheckbox', 'isamounteditable', get_string('isamounteditable', 'paygw_expresspay'), 0);
        $mform->addHelpButton('isamounteditable', 'isamounteditable', 'paygw_expresspay');

        $mform->addElement('advcheckbox', 'sendemail', get_string('sendemail', 'paygw_expresspay'), 0);
        $mform->addHelpButton('sendemail', 'sendemail', 'paygw_expresspay');

        $mform->addElement('advcheckbox', 'sendsms', get_string('sendsms', 'paygw_expresspay'), 0);
        $mform->addHelpButton('sendsms', 'sendsms', 'paygw_expresspay');
    }

    /**
     * Validates the gateway configuration form.
     *
     * @param \core_payment\form\account_gateway $form
     * @param \stdClass $data
     * @param array $files
     * @param array $errors form errors (passed by reference)
     */
    public static function validate_gateway_form(
        \core_payment\form\account_gateway $form,
        \stdClass $data,
        array $files,
        array &$errors
    ): void {
        if (
            $data->enabled &&
            (empty($data->serviceno) || empty($data->token))
        ) {
            $errors['enabled'] = get_string('gatewaycannotbeenabled', 'payment');
        }
    }
}
