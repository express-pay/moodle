<?php
/**
 * Settings for the ExpressPay payment gateway
 *
 * @package    paygw_expresspay
 * @copyright  LLC TriIncom <info@express-pay.by>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('paygw_expresspay_settings', '', get_string('pluginname_desc', 'paygw_expresspay')));

    \core_payment\helper::add_common_gateway_settings($settings, 'paygw_expresspay');
}
